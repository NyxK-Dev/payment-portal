<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Permissions extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();


        $this->load->service(
            'Permission_service'
        );


        // TODO: Add authorization
        // $this->auth->authorize('permissions.manage');

    }





    public function index()
    {

        $data = array(

            'title' => 'Permissions',

            'page_heading' => 'Permissions',

            'page_description' =>
                'Manage system permissions.',


            'permissions' =>
                $this->Permission_service
                ->getPermissions(),


            'breadcrumbs' => array(

                'Home' => '',

                'Permissions' => NULL,

            ),

        );



        $this->render(
            'admin/permissions/index',
            $data
        );

    }







    public function create()
    {

        $data = array(

            'title' => 'Create Permission',

            'page_heading' => 'Create Permission',

            'breadcrumbs' => array(

                'Home' => '',

                'Permissions' =>
                    'admin/permissions',

                'Create' => NULL,

            ),

        );



        $this->render(
            'admin/permissions/create',
            $data
        );

    }







    public function store()
    {


        $data = array(

            'code' =>
                trim(
                    $this->input->post('code')
                ),


            'name' =>
                trim(
                    $this->input->post('name')
                ),


            'description' =>
                trim(
                    $this->input->post('description')
                ),

        );




        try {


            $this->Permission_service
                ->create($data);



            redirect(
                'admin/permissions'
            );


        } catch(Exception $e) {


            show_error(
                $e->getMessage()
            );


        }


    }







    public function edit($id)
    {


        $data = array(

            'title' => 'Edit Permission',

            'page_heading' => 'Edit Permission',


            'permission' =>
                $this->Permission_service
                ->getPermission($id),


            'breadcrumbs' => array(

                'Home' => '',

                'Permissions' =>
                    'admin/permissions',

                'Edit' => NULL,

            ),

        );



        $this->render(
            'admin/permissions/edit',
            $data
        );


    }







    public function update($id)
    {


        $data = array(

            'code' =>
                trim(
                    $this->input->post('code')
                ),


            'name' =>
                trim(
                    $this->input->post('name')
                ),


            'description' =>
                trim(
                    $this->input->post('description')
                ),

        );




        try {


            $this->Permission_service
                ->update(
                    $id,
                    $data
                );



            redirect(
                'admin/permissions'
            );


        } catch(Exception $e) {


            show_error(
                $e->getMessage()
            );


        }


    }







    public function delete($id)
    {


        try {


            $this->Permission_service
                ->delete($id);



            redirect(
                'admin/permissions'
            );


        } catch(Exception $e) {


            show_error(
                $e->getMessage()
            );


        }


    }


}