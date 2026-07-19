<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_service
{
    /**
     * @var CI_Controller
     */
    protected $CI;
    protected $userrepository;
    protected $auth;

    public function __construct(
        UserRepositoryInterface $userrepository,
        Auth $auth
    ) {
        $this->CI = &get_instance();

        $this->userrepository = $userrepository;
        $this->auth = $auth;
    }

    /**
     * Attempt to authenticate a user.
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function attempt($email, $password)
    {
        if (empty($email) || empty($password)) {

            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
        $user = $this->userrepository->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.',
            ];
        }

        if (!empty($user->deleted_at)) {
            return [
                'success' => false,
                'message' => 'This account has been deleted.',
            ];
        }

        $statusLookup = $this->CI->db
            ->select('lookups.code')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'user_status')
            ->where('lookups.id', $user->status_lookup_id)
            ->get()
            ->row();

        if (empty($statusLookup) || $statusLookup->code !== 'active') {
            return [
                'success' => false,
                'message' => 'Your account is inactive.',
            ];
        }

        if (!password_verify($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.',
            ];
        }

        $this->userrepository->updateLastLogin($user->id);

        $this->auth->login($user);

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    /**
     * Logout current user.
     */
    public function logout()
    {
        $this->auth->logout();
    }

    /**
     * Hash password.
     *
     * @param string $password
     * @return string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
