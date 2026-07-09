<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->service('Role_service');

        // Enable after authentication is ready
        // $this->auth->authorize('roles.manage');
    }

    public function index()
    {
        $this->render('admin/roles/index', array(
            'title'            => 'Role Management',
            'page_heading'     => 'Roles',
            'page_description' => 'Manage system roles.',
            'breadcrumbs'      => array(
                'Dashboard' => site_url('admin/dashboard'),
                'Roles'     => NULL,
            ),
            'roles' => $this->Role_service->getRoles(),
        ));
    }

    public function create()
    {
        $this->render('admin/roles/create', array(
            'title'            => 'Create Role',
            'page_heading'     => 'Create Role',
            'page_description' => 'Add a new role.',
            'breadcrumbs'      => array(
                'Dashboard' => site_url('admin/dashboard'),
                'Roles'     => site_url('admin/roles'),
                'Create'    => NULL,
            ),
        ));
    }

    public function store()
    {
        $data = array(
            'name'        => trim($this->input->post('name', TRUE)),
            'description' => trim($this->input->post('description', TRUE)),
        );

        $this->Role_service->create($data);

        redirect('admin/roles');
    }

    public function edit($id)
    {
        $this->render('admin/roles/edit', array(
            'title'            => 'Edit Role',
            'page_heading'     => 'Edit Role',
            'page_description' => 'Update role information.',
            'breadcrumbs'      => array(
                'Dashboard' => site_url('admin/dashboard'),
                'Roles'     => site_url('admin/roles'),
                'Edit'      => NULL,
            ),
            'role' => $this->Role_service->getRole($id),
        ));
    }

    public function update($id)
    {
        $data = array(
            'name'        => trim($this->input->post('name', TRUE)),
            'description' => trim($this->input->post('description', TRUE)),
        );

        $this->Role_service->update($id, $data);

        redirect('admin/roles');
    }

    public function delete($id)
    {
        $this->Role_service->delete($id);

        redirect('admin/roles');
    }
}