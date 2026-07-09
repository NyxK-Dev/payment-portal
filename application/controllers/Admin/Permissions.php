<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Permissions extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();


        $this->load->service(
            'PermissionService'
        );


        $this->load->library(
            'RequestValidator'
        );

    }





    public function index()
    {


        $data = [

            'title'=>'Permissions',

            'page_heading'=>'Permissions',

            'page_description'=>
                'Manage system permissions.',



            'permissions'=>
                $this->permissionservice
                ->getPermissions(),



            'breadcrumbs'=>[

                'Home'=>'',

                'Permissions'=>NULL

            ]

        ];



        $this->render(
            'admin/permissions/index',
            $data
        );

    }







    public function create()
    {


        $this->render(
            'admin/permissions/create',
            [

                'title'=>'Create Permission',

                'page_heading'=>'Create Permission',

                'breadcrumbs'=>[

                    'Home'=>'',

                    'Permissions'=>
                        site_url('admin/permissions'),

                    'Create'=>NULL

                ]

            ]
        );


    }







    public function store()
    {


        if(
            !$this->requestvalidator
            ->validate(
                'Permission',
                'create'
            )
        )
        {


            $this->render(
                'admin/permissions/create',
                [

                    'title'=>'Create Permission',

                    'errors'=>
                    $this->form_validation
                    ->error_array()

                ]
            );


            return;

        }





        $data=[


            'code'=>
                trim(
                    $this->input->post(
                        'code',
                        TRUE
                    )
                ),



            'name'=>
                trim(
                    $this->input->post(
                        'name',
                        TRUE
                    )
                ),



            'description'=>
                trim(
                    $this->input->post(
                        'description',
                        TRUE
                    )
                )

        ];


        try
        {


            $this->permissionservice
                ->create(
                    $data
                );



            $this->session
                ->set_flashdata(
                    'success',
                    'Permission created successfully.'
                );



            redirect(
                'admin/permissions'
            );


        }
        catch(Exception $e)
        {

            $this->render(
                'admin/permissions/create',
                [

                    'title'=>'Create Permission',

                    'errors'=>[

                        'code'=>
                        $e->getMessage()

                    ]

                ]
            );

        }



    }


    public function edit($id)
    {


        $data=[

            'title'=>'Edit Permission',

            'page_heading'=>'Edit Permission',


            'permission'=>
                $this->permissionservice
                ->getPermission($id)


        ];



        $this->render(
            'admin/permissions/edit',
            $data
        );


    }


    public function update($id)
    {


        if(
            !$this->requestvalidator
            ->validate(
                'Permission',
                'update'
            )
        )
        {


            $this->render(
                'admin/permissions/edit',
                [

                    'title'=>'Edit Permission',


                    'permission'=>
                    $this->permissionservice
                    ->getPermission($id),


                    'errors'=>
                    $this->form_validation
                    ->error_array()

                ]
            );


            return;

        }





        $data=[


            'code'=>
                trim(
                    $this->input->post(
                        'code',
                        TRUE
                    )
                ),



            'name'=>
                trim(
                    $this->input->post(
                        'name',
                        TRUE
                    )
                ),



            'description'=>
                trim(
                    $this->input->post(
                        'description',
                        TRUE
                    )
                )


        ];


        try
        {


            $this->permissionservice
                ->update(
                    $id,
                    $data
                );



            $this->session
                ->set_flashdata(
                    'success',
                    'Permission updated successfully.'
                );



            redirect(
                'admin/permissions'
            );


        }
        catch(Exception $e)
        {


            $this->render(
                'admin/permissions/edit',
                [

                    'title'=>'Edit Permission',


                    'permission'=>
                    $this->permissionservice
                    ->getPermission($id),



                    'errors'=>[

                        'code'=>
                        $e->getMessage()

                    ]

                ]
            );


        }


    }


    public function delete($id)
    {


        try
        {


            $this->permissionservice
                ->delete(
                    $id
                );



            $this->session
                ->set_flashdata(
                    'success',
                    'Permission deleted successfully.'
                );



            redirect(
                'admin/permissions'
            );


        }
        catch(Exception $e)
        {

            show_error(
                $e->getMessage()
            );

        }


    }



}