<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Api_Controller.php';


class JwtTest extends MY_Api_Controller
{

    public function index()
    {

        $this->load->library(
            'JwtLibrary',
            null,
            'jwtLibrary'
        );


        $token = $this->jwtLibrary->generateToken(
            [
                'id' => 1,
                'email' => 'admin@test.com',
                'role' => 'admin'
            ]
        );


        $this->sendResponse(
            [
                'token' => $token
            ],
            'JWT generated'
        );
    }
}
