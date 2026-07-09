<?php
defined('BASEPATH') or exit('No direct script access allowed');


class LookupGroups extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();


        $this->load->library(
            'RequestValidator'
        );


        $this->load->service(
            'LookupGroupService'
        );
    }

    public function index()
    {


        $data = [

            'title'=>'Lookup Groups',

            'content'=>
                'admin/lookupgroups/index',

            'lookupgroups'=>
                $this->lookupgroupservice
                     ->getAll()

        ];



        $this->load->view(
            'layouts/app_layout',
            $data
        );

    }

    public function create()
    {
        $data = [
            'title' => 'Create Lookup Group',
            'content' => 'admin/lookupgroups/create'
        ];

        $this->load->view(
            'layouts/app_layout',
            $data
        );
    }


    public function store()
    {


        if (
            !$this->requestvalidator
                ->validate(
                    'LookupGroup',
                    'create'
                )
        ) {

            echo validation_errors();

            return;
        }



        $data = [

            'code' =>
            $this->input->post('code'),

            'name' =>
            $this->input->post('name'),

            'description' =>
            $this->input->post('description')

        ];



        $id =
            $this->lookupgroupservice
            ->create($data);



        $this->session->set_flashdata('success', 'Lookup group created successfully.');

        redirect('admin/lookupgroups');
    }
}
