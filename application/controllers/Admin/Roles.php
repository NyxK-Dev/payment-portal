<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Roles extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        // $this->auth->deny(
        //     'manage_roles'
        // );

        $this->load->library(
            'RequestValidator'
        );


        $this->load->service(
            'RoleService'
        );
        
    }



// public function index()
// {
//     echo "<pre>";

//     var_dump(
//         $this->auth->can('manage_roles')
//     );

//     echo "</pre>";
//     exit;
// }

    public function index()
    {
        

        $data = [

            'title' => 'Roles',

            'page_heading' => 'Roles',

            'page_description' =>
            'Manage system roles.',


            'breadcrumbs' => [

                'Dashboard' =>
                site_url('admin/dashboard'),

                'Roles' =>
                NULL,

            ],



            'roles' =>
            $this->roleservice
                ->getRoles()

        ];



        $this->render(
            'admin/roles/index',
            $data
        );
    }

    public function create()
    {

        $this->render(
            'admin/roles/create',
            [

                'title' =>
                'Create Role',


                'page_heading' =>
                'Create Role',


                'page_description' =>
                'Create a new role.',



                'breadcrumbs' => [

                    'Dashboard' =>
                    site_url('admin/dashboard'),

                    'Roles' =>
                    site_url('admin/roles'),

                    'Create' =>
                    NULL

                ]

            ]
        );
    }


    public function store()
    {

        if (
            !$this->requestvalidator
                ->validate(
                    'Role',
                    'create'
                )
        ) {

            $this->render(
                'admin/roles/create',
                [

                    'title' => 'Create Role',


                    'page_heading' => 'Create Role',


                    'errors' =>
                    $this->form_validation
                        ->error_array()

                ]
            );


            return;
        }





        $data = [

            'name' =>
            trim(
                $this->input->post(
                    'name',
                    TRUE
                )
            ),


            'description' =>
            trim(
                $this->input->post(
                    'description',
                    TRUE
                )
            )

        ];






        try {


            $this->roleservice
                ->create(
                    $data
                );




            $this->session
                ->set_flashdata(
                    'success',
                    'Role created successfully.'
                );



            redirect(
                'admin/roles'
            );
        } catch (Exception $e) {


            $this->render(
                'admin/roles/create',
                [

                    'title' => 'Create Role',


                    'page_heading' => 'Create Role',



                    'errors' => [

                        'name' =>
                        $e->getMessage()

                    ]

                ]
            );
        }
    }

    public function edit($id)
    {

        $this->render(
            'admin/roles/edit',
            [

                'title' =>
                'Edit Role',


                'page_heading' =>
                'Edit Role',


                'page_description' =>
                'Update role information.',



                'breadcrumbs' => [

                    'Dashboard' =>
                    site_url('admin/dashboard'),

                    'Roles' =>
                    site_url('admin/roles'),

                    'Edit' =>
                    NULL

                ],



                'role' =>
                $this->roleservice
                    ->getRole($id)

            ]
        );
    }


    public function update($id)
    {

        if (
            !$this->requestvalidator
                ->validate(
                    'Role',
                    'update'
                )
        ) {

            $this->render(
                'admin/roles/edit',
                [

                    'title' => 'Edit Role',


                    'role' =>
                    $this->roleservice
                        ->getRole($id),


                    'errors' =>
                    $this->form_validation
                        ->error_array()

                ]
            );

            return;
        }




        $data = [

            'name' =>
            trim(
                $this->input->post(
                    'name',
                    TRUE
                )
            ),


            'description' =>
            trim(
                $this->input->post(
                    'description',
                    TRUE
                )
            )

        ];




        try {

            $this->roleservice
                ->update(
                    $id,
                    $data
                );


            $this->session
                ->set_flashdata(
                    'success',
                    'Role updated successfully.'
                );


            redirect(
                'admin/roles'
            );
        } catch (Exception $e) {

            $this->render(
                'admin/roles/edit',
                [

                    'title' => 'Edit Role',


                    'role' =>
                    $this->roleservice
                        ->getRole($id),


                    'errors' => [

                        'name' =>
                        $e->getMessage()

                    ]

                ]
            );
        }
    }


    public function delete($id)
    {


        try {


            $this->roleservice
                ->delete(
                    $id
                );



            $this->session
                ->set_flashdata(
                    'success',
                    'Role deleted successfully.'
                );



            redirect(
                'admin/roles'
            );
        } catch (Exception $e) {

            show_error(
                $e->getMessage()
            );
        }
    }
}
