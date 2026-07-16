<?php
defined('BASEPATH') or exit('No direct script access allowed');


class TokenService
{

    protected $CI;


    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->library(
            'JwtLibrary',
            null,
            'jwtLibrary'
        );
    }



    public function createAccessToken($user)
    {

        return $this->CI->jwtLibrary->generateToken([

            'id' => $user->id,

            'email' => $user->email,

            'role_id' => $user->role_id

        ], 3600);
    }



    public function createRefreshToken($user)
    {

        return $this->CI->jwtLibrary->generateToken([

            'id' => $user->id,

            'type' => 'refresh'

        ], 2592000);
    }



    public function verify($token)
    {

        return $this->CI
            ->jwtLibrary
            ->verifyToken($token);
    }
}
