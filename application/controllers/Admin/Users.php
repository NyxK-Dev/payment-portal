<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Users extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();


        $this->load->service(
            'UserService'
        );


        $this->load->service(
            'RoleService'
        );


        $this->load->library(
            'RequestValidator'
        );
    }



    public function index()
    {

        $data = [

            'title' => 'Users',

            'page_heading' => 'Users',


            'users' =>
            $this->userservice
                ->getUsers(),


            'roles' =>
            $this->roleservice
                ->getRoles()

        ];



        $this->render(
            'admin/users/index',
            $data
        );
    }





    public function updateRole($id)
    {


        if (
            !$this->requestvalidator
                ->validate(
                    'User',
                    'updateRole'
                )
        ) {

            redirect(
                'admin/users'
            );

            return;
        }



        $roleId =
            $this->input
            ->post('role_id', TRUE);



        $this->userservice
            ->changeRole(
                $id,
                $roleId
            );



        $this->session
            ->set_flashdata(
                'success',
                'User role updated successfully.'
            );



        redirect(
            'admin/users'
        );
    }
}
