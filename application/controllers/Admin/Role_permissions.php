<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Role_permissions extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();


        $this->load->service(
            'Role_permission_service'
        );


        $this->load->service(
            'Role_service'
        );


        $this->load->service(
            'Permission_service'
        );

    }




    public function index()
    {

        $this->render(
            'admin/role_permissions/index',
            array(

                'title' => 'Role Permissions',

                'page_heading' => 'Role Permissions',

                'page_description' =>
                    'Manage role permissions.',


                'breadcrumbs' => array(

                    'Home' => '',

                    'Role Permissions' => NULL

                ),


                'role_permissions' =>
                    $this->Role_permission_service
                    ->getAll()

            )
        );

    }







    public function create()
    {

        $this->render(
            'admin/role_permissions/create',
            array(

                'title' => 'Assign Permission',

                'page_heading' =>
                    'Assign Permission',


                'page_description' =>
                    'Assign multiple permissions to a role.',



                'breadcrumbs' => array(

                    'Home' => '',

                    'Role Permissions' =>
                        'admin/role_permissions',

                    'Create' => NULL

                ),



                'roles' =>
                    $this->Role_service
                    ->getRoles(),



                'permissions' =>
                    $this->Permission_service
                    ->getPermissions()

            )
        );

    }







   public function store()
{

    $role_id =
        $this->input->post('role_id');


    $permission_ids =
        $this->input->post('permission_id');



    $this->Role_permission_service
        ->assignPermissions(
            $role_id,
            $permission_ids
        );



    redirect(
        'admin/role_permissions'
    );

}







    public function edit_role($role_id)
    {

        $this->render(
            'admin/role_permissions/edit',
            array(

                'title' =>
                    'Edit Role Permissions',


                'page_heading' =>
                    'Edit Role Permissions',


                'page_description' =>
                    'Update assigned permissions.',



                'breadcrumbs' => array(

                    'Home' => '',

                    'Role Permissions' =>
                        'admin/role_permissions',

                    'Edit' => NULL

                ),



                'role_id' =>
                    $role_id,



                'roles' =>
                    $this->Role_service
                    ->getRoles(),



                'permissions' =>
                    $this->Permission_service
                    ->getPermissions(),



                'assigned_permissions' =>
                    $this->Role_permission_service
                    ->getPermissionIdsByRole(
                        $role_id
                    )

            )
        );

    }







    public function update($role_id)
    {

        $permission_ids =
            $this->input->post('permission_id');



        $this->Role_permission_service
            ->updatePermissions(
                $role_id,
                $permission_ids
            );



        redirect(
            'admin/role_permissions'
        );

    }







    public function delete_role($role_id)
    {

        $this->Role_permission_service
            ->deleteByRole(
                $role_id
            );



        redirect(
            'admin/role_permissions'
        );

    }


}