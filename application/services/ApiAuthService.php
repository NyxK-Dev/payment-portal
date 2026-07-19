<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/ApiException.php';

class ApiAuthService
{
    protected $CI;

    protected $accessTokenLifetime = 900; //15 minutes
    protected $refreshTokenLifetime = 604800; //7 days

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->repository('UserRepository');
        $this->CI->load->library('JwtLibrary');
        $this->CI->load->service('Verification_service');
    }

    /**
     * Authenticate user credentials and return access + refresh tokens
     */
    public function login(string $email, string $password): array
    {
        $user = $this->CI->userrepository->findByEmail($email);

        if (!$user) {
            throw new ApiException('Invalid credentials provided.', 401);
        }

        if (!password_verify($password, $user->password)) {
            throw new ApiException('Invalid credentials provided.', 401);
        }

        $pendingStatus = $this->CI->db->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'user_status')
            ->where('lookups.code', 'inactive')
            ->get()
            ->row();

        if ($pendingStatus && (int)$user->status_lookup_id === (int)$pendingStatus->id) {
            throw new ApiException('Your email address is not verified.', 403);
        }

        $this->CI->userrepository->updateLastLogin($user->id);

        $payload = [
            'id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role_name
        ];

        $accessToken = $this->CI->jwtlibrary->generateToken(
            $payload,
            $this->accessTokenLifetime
        );

        $rawRefreshToken = bin2hex(random_bytes(64));

        $hashedToken = hash(
            'sha256',
            $rawRefreshToken
        );

        $tokenFamily = bin2hex(
            random_bytes(32)
        );

        $this->CI->db->insert('refresh_tokens', [
            'user_id' => $user->id,
            'token_hash' => $hashedToken,
            'token_family' => $tokenFamily,
            'expires_at' => date(
                'Y-m-d H:i:s',
                time() + $this->refreshTokenLifetime
            ),
            'ip_address' => $this->CI->input->ip_address(),
            'user_agent' => $this->CI->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s'),
            'last_used_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $rawRefreshToken,
            'expires_in' => $this->accessTokenLifetime,
            'refresh_expires_in' => $this->refreshTokenLifetime,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role_name
            ]
        ];
    }

    /**
     * Create accounts and hand off logic directly to your Redis verification workflow
     */
    public function register(array $data): array
    {
        if (empty($data['email']) || empty($data['name']) || empty($data['password'])) {
            throw new ApiException('Registration data is incomplete.', 422);
        }

        $existingUser = $this->CI->userrepository->findByEmail($data['email']);

        if ($existingUser) {
            throw new ApiException('Email is already registered under another account.', 422);
        }

        $customerRole = $this->CI->userrepository->getRoleByName('customer');

        if (!$customerRole) {
            throw new Exception('System Error: Customer access role configuration is missing.');
        }

        $pendingStatus = $this->CI->db->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'user_status')
            ->where('lookups.code', 'inactive')
            ->get()
            ->row();

        if (!$pendingStatus) {
            throw new Exception('System Error: Account state workflow configurations missing.');
        }

        $this->CI->db->trans_start();

        $userId = $this->CI->userrepository->create([
            'name' => trim($data['name']),
            'email' => trim(strtolower($data['email'])),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => $customerRole->id,
            'status_lookup_id' => $pendingStatus->id
        ]);

        $this->CI->db->trans_complete();

        if ($this->CI->db->trans_status() === FALSE) {
            throw new ApiException(
                'Database runtime transaction error processing user creation.',
                500
            );
        }

        $this->CI->verification_service->resendCode($userId, 60);

        return [
            'user_id' => $userId,
            'email' => $data['email'],
            'status' => 'pending_verification'
        ];
    }
    /**
     * Exchange refresh token and rotate refresh token
     */
    public function refresh(string $rawRefreshToken): array
    {
        $tokenHash = hash(
            'sha256',
            $rawRefreshToken
        );

        $storedToken = $this->CI->db
            ->where('token_hash', $tokenHash)
            ->where('revoked_at IS NULL', null, false)
            ->get('refresh_tokens')
            ->row();

        if (!$storedToken) {
            throw new ApiException(
                'Refresh token invalid or already revoked.',
                401
            );
        }

        if (strtotime($storedToken->expires_at) <= time()) {

            $this->CI->db
                ->where('id', $storedToken->id)
                ->update(
                    'refresh_tokens',
                    [
                        'revoked_at' => date('Y-m-d H:i:s')
                    ]
                );

            throw new ApiException(
                'Refresh token expired. Please login again.',
                401
            );
        }

        $user = $this->CI->db
            ->select('users.*, roles.name as role_name')
            ->from('users')
            ->join(
                'roles',
                'roles.id = users.role_id'
            )
            ->where(
                'users.id',
                $storedToken->user_id
            )
            ->where(
                'users.deleted_at',
                null
            )
            ->get()
            ->row();

        if (!$user) {
            throw new ApiException(
                'User account not found.',
                401
            );
        }

        /*
         * Revoke old refresh token
         */
        $this->CI->db
            ->where(
                'id',
                $storedToken->id
            )
            ->update(
                'refresh_tokens',
                [
                    'revoked_at' => date('Y-m-d H:i:s')
                ]
            );


        /*
         * Generate new refresh token
         */
        $newRefreshToken = bin2hex(
            random_bytes(64)
        );

        $newRefreshHash = hash(
            'sha256',
            $newRefreshToken
        );


        /*
         * Store new refresh token
         */
        $this->CI->db->insert(
            'refresh_tokens',
            [
                'user_id' => $user->id,
                'token_hash' => $newRefreshHash,
                'token_family' => $storedToken->token_family,
                'expires_at' => date(
                    'Y-m-d H:i:s',
                    time() + $this->refreshTokenLifetime
                ),
                'ip_address' => $this->CI->input->ip_address(),
                'user_agent' => $this->CI->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s'),
                'last_used_at' => date('Y-m-d H:i:s')
            ]
        );


        $payload = [
            'id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role_name
        ];


        $newAccessToken = $this->CI->jwtlibrary->generateToken(
            $payload,
            $this->accessTokenLifetime
        );


        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'expires_in' => $this->accessTokenLifetime,
            'refresh_expires_in' => $this->refreshTokenLifetime
        ];
    }


    /**
     * Process registration verification code
     */
    public function verifyRegistrationCode(
        int $userId,
        string $code,
        string $ipAddress
    ): bool {

        list($allowed, $remainingAttempts) =
            $this->CI->verification_service
            ->recordAttempt(
                $userId,
                $ipAddress
            );

        if (!$allowed) {
            throw new ApiException(
                'Too many incorrect verification attempts. Please wait before trying again.',
                429
            );
        }


        $isCorrect =
            $this->CI->verification_service
            ->verifyCode(
                $userId,
                $code
            );


        if (!$isCorrect) {

            throw new ApiException(
                'Invalid or expired verification code. Attempts remaining: ' . $remainingAttempts,
                400
            );
        }


        $this->CI->verification_service
            ->resetAttempts(
                $userId,
                $ipAddress
            );

        return true;
    }


    /**
     * Request new registration verification code
     */
    public function resendRegistrationCode(string $email): bool
    {
        $user = $this->CI->userrepository->findByEmail($email);

        if (!$user) {
            throw new ApiException(
                'Email account address not found.',
                404
            );
        }
        list($sent, $reason) =
            $this->CI->verification_service
            ->resendCode(
                $user->id,
                60
            );


        if (!$sent) {

            if ($reason === 'cooldown') {

                throw new ApiException(
                    'Please wait before requesting another verification code.',
                    429
                );
            }


            if ($reason === 'daily_limit') {

                throw new ApiException(
                    'Maximum daily verification resend limit reached.',
                    429
                );
            }


            throw new ApiException(
                'Unable to resend code: ' . $reason,
                400
            );
        }


        return true;
    }


    /**
     * Revoke refresh token during logout
     */
    public function logout(string $rawRefreshToken): bool
    {
        $tokenHash = hash(
            'sha256',
            $rawRefreshToken
        );


        $this->CI->db
            ->where(
                'token_hash',
                $tokenHash
            )
            ->update(
                'refresh_tokens',
                [
                    'revoked_at' => date('Y-m-d H:i:s')
                ]
            );


        return true;
    }
}
