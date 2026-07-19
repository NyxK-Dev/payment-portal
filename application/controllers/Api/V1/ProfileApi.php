<?php
defined('BASEPATH') or exit('No direct script access allowed');


require_once APPPATH . 'core/MY_Api_Controller.php';


class ProfileApi extends MY_Api_Controller
{


    public function index()
    {

        $user =
            $this->requireAuth();



        $this->sendResponse(
            [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ],
            'Authenticated user'
        );
    }
}
