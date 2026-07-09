<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lookups extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('RequestValidator');

        // Load both services so we can display group context
        $this->load->service('LookupGroupService');
        $this->load->service('LookupService');
    }

    /**
     * Lists values belonging to a specific lookup group
     * Maps to: admin/lookups AND admin/lookups/(:num)
     */
    public function index($groupId = null)
    {
        if (empty($groupId)) {
            // 1. No Group ID: Fetch ALL lookups across all categories
            $data = [
                'title'   => 'All Lookup Values',
                'content' => 'admin/lookups/index', // We will make this view next
                'lookups' => $this->lookupservice->getAllWithGroup()
            ];

            $this->load->view('layouts/app_layout', $data);
            return;
        }

        // 2. Group ID exists: Show lookups filtered by group (Your original logic)
        $group = $this->lookupgroupservice->find($groupId);
        if (!$group) {
            show_404();
        }

        $data = [
            'title'   => 'Manage Values: ' . $group->name,
            'content' => 'admin/lookups/index',
            'group'   => $group,
            'lookups' => $this->lookupservice->getByGroup($groupId)
        ];

        $this->load->view('layouts/app_layout', $data);
    }
    /**
     * View for creating a new value under a specific group
     * Maps to: admin/lookups/create/(:num)
     */
    public function create($groupId)
    {
        $group = $this->lookupgroupservice->find($groupId);
        if (!$group) {
            show_404();
        }

        $data = [
            'title'   => 'Add Value to ' . $group->name,
            'content' => 'admin/lookups/create',
            'group'   => $group
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    /**
     * Store the value
     * Maps to: admin/lookups/store/(:num)
     */
    public function store($groupId)
    {
        if (!$this->lookupgroupservice->find($groupId)) {
            show_404();
        }

        if (!$this->requestvalidator->validate('Lookup', 'create')) {
            echo validation_errors();
            return;
        }

        $data = [
            'group_id'     => $groupId,
            'code'         => $this->input->post('code'),
            'value'        => $this->input->post('value'),
            'description'  => $this->input->post('description'),
            'sort_order'   => $this->input->post('sort_order') ?: 0,
            'is_active'    => $this->input->post('is_active')
        ];

        $this->lookupservice->create($data);

        $this->session->set_flashdata('success', 'Value added successfully.');

        // This will redirect them right back to their lookups table view smoothly!
        redirect('admin/lookups/' . $groupId);
    }
    /**
     * Display edit form
     * Maps to: admin/lookups/edit/(:num)
     */
    public function edit($id)
    {
        $lookup = $this->lookupservice->find($id);

        if (!$lookup) {
            show_404();
        }

        $group = $this->lookupgroupservice->find($lookup->group_id);

        if (!$group) {
            show_404();
        }

        $data = [
            'title'   => 'Edit Lookup Value',
            'content' => 'admin/lookups/edit',
            'group'   => $group,
            'lookup'  => $lookup
        ];

        $this->load->view('layouts/app_layout', $data);
    }

    /**
     * Update lookup value
     * Maps to: admin/lookups/update/(:num)
     */
    public function update($id)
    {
        $lookup = $this->lookupservice->find($id);
        // echo '<pre>';
        // print_r($lookup);
        // print_r($group);
        // exit;

        if (!$lookup) {
            show_404();
        }

        if (!$this->requestvalidator->validate('Lookup', 'update')) {
            echo validation_errors();
            return;
        }

        $data = [
            'code'        => $this->input->post('code'),
            'value'       => $this->input->post('value'),
            'description' => $this->input->post('description'),
            'sort_order'  => $this->input->post('sort_order') ?: 0,
            'is_active'   => $this->input->post('is_active')
        ];

        $this->lookupservice->update($id, $data);

        $this->session->set_flashdata(
            'success',
            'Lookup value updated successfully.'
        );

        redirect('admin/lookups');
    }

    /**
     * Delete lookup value
     * Maps to: admin/lookups/delete/(:num)
     */
    public function delete($id)
    {
        $lookup = $this->lookupservice->find($id);

        if (!$lookup) {
            show_404();
        }

        $groupId = $lookup->group_id;

        $this->lookupservice->delete($id);

        $this->session->set_flashdata(
            'success',
            'Lookup value deleted successfully.'
        );

        // redirect('admin/lookups/' . $groupId);
        redirect('admin/lookups');
    }
}
