<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LookupGroups extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('RequestValidator');
        $this->load->service('LookupGroupService');
        $this->load->service('LookupService');
    }

    public function index()
    {
        $data = [
            'title' => 'Lookup Groups',
            'content' => 'admin/lookupgroups/index',
            'lookupgroups' => $this->lookupgroupservice->getAll()
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Lookup Group',
            'content' => 'admin/lookupgroups/create'
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    public function store()
    {
        if (!$this->requestvalidator->validate('LookupGroup', 'create')) {
            echo validation_errors();
            return;
        }

        $data = [
            'code' => $this->input->post('code'),
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description')
        ];

        $this->lookupgroupservice->create($data);

        $this->session->set_flashdata(
            'success',
            'Lookup group created successfully.'
        );

        redirect('admin/lookupgroups');
    }

    /**
     * Display edit form
     */
    public function edit($id)
    {
        $lookupgroup = $this->lookupgroupservice->find($id);

        if (!$lookupgroup) {
            show_404();
        }

        $data = [
            'title' => 'Edit Lookup Group',
            'content' => 'admin/lookupgroups/edit',
            'lookupgroup' => $lookupgroup
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    /**
     * Update lookup group
     */
    public function update($id)
    {
        $lookupgroup = $this->lookupgroupservice->find($id);

        if (!$lookupgroup) {
            show_404();
        }

        if (!$this->requestvalidator->validate('LookupGroup', 'update')) {
            echo validation_errors();
            return;
        }

        $data = [
            'code' => $this->input->post('code'),
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description')
        ];

        $this->lookupgroupservice->update($id, $data);

        $this->session->set_flashdata(
            'success',
            'Lookup group updated successfully.'
        );

        redirect('admin/lookupgroups');
    }

    /**
     * Delete lookup group
     */
    public function delete($id)
    {
        $lookupgroup = $this->lookupgroupservice->find($id);

        if (!$lookupgroup) {
            show_404();
        }

        // Check whether this group still has lookup values
        $count = $this->lookupservice->countByGroup($id);

        if ($count > 0) {

            $this->session->set_flashdata(
                'error',
                "Cannot delete this lookup group because it contains {$count} lookup value(s)."
            );

            redirect('admin/lookupgroups');
        }

        // Safe to delete
        $this->lookupgroupservice->delete($id);

        $this->session->set_flashdata(
            'success',
            'Lookup group deleted successfully.'
        );

        redirect('admin/lookupgroups');
    }
}
