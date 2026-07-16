<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JwtLibrary
{
    protected $secretKey;
    protected $algorithm = 'HS256';

    public function __construct()
    {
        $this->secretKey = config_item('jwt_secret') ?: 'payment_portal_secret_key';
    }

    /**
     * Generate short-lived stateless Access JWT
     */
    public function generateToken(array $payload, int $expire = 120): string
    {
        $issuedAt = time();
        $expireAt = $issuedAt + $expire;

        $tokenPayload = [
            'iat' => $issuedAt,
            'exp' => $expireAt,
            'data' => $payload
        ];

        return JWT::encode(
            $tokenPayload,
            $this->secretKey,
            $this->algorithm
        );
    }

    /**
     * Verify Token
     */
    public function verifyToken(string $token)
    {
        try {
            return [
                'success' => true,
                'payload' => JWT::decode(
                    $token,
                    new Key(
                        $this->secretKey,
                        $this->algorithm
                    )
                )
            ];
        } catch (ExpiredException $e) {
            return [
                'success' => false,
                'error' => 'expired'
            ];
        } catch (SignatureInvalidException $e) {
            return [
                'success' => false,
                'error' => 'invalid_signature'
            ];
        } catch (BeforeValidException $e) {
            return [
                'success' => false,
                'error' => 'not_active'
            ];
        } catch (UnexpectedValueException $e) {
            return [
                'success' => false,
                'error' => 'invalid_token'
            ];
        }
    }

    /**
     * Extract token payload
     */
    public function getPayload(string $token)
    {
        $result = $this->verifyToken($token);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'data' => (array) $result['payload']->data
        ];
    }
}
