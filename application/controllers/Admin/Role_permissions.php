<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_permissions extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('RequestValidator');

        $this->load->service('RolePermissionService');
        $this->load->service('RoleService');
        $this->load->service('PermissionService');
    }

    public function index()
    {
        $this->render(
            'admin/role_permissions/index',
            [
                'title' => 'Role Permissions',
                'page_heading' => 'Role Permissions',
                'page_description' => 'Manage role permissions.',
                'breadcrumbs' => [
                    'Home' => '',
                    'Role Permissions' => NULL
                ],
                'role_permissions' => $this->rolepermissionservice->getAll()
            ]
        );
    }

    public function create()
    {
        $this->render(
            'admin/role_permissions/create',
            [
                'title' => 'Assign Permission',
                'page_heading' => 'Assign Permission',
                'page_description' => 'Assign multiple permissions to a role.',
                'breadcrumbs' => [
                    'Home' => '',
                    'Role Permissions' => site_url('admin/role_permissions'),
                    'Create' => NULL
                ],
                'roles' => $this->roleservice->getRoles(),
                'permissions' => $this->permissionservice->getPermissions()
            ]
        );
    }

    public function store()
    {
        if (!$this->requestvalidator->validate('RolePermission', 'create')) {

            $this->render(
                'admin/role_permissions/create',
                [
                    'title' => 'Assign Permission',
                    'roles' => $this->roleservice->getRoles(),
                    'permissions' => $this->permissionservice->getPermissions(),
                    'errors' => $this->form_validation->error_array()
                ]
            );

            return;
        }

        $this->rolepermissionservice->assignPermissions(
            $this->input->post('role_id'),
            $this->input->post('permission_id')
        );

        redirect('admin/role_permissions');
    }

    public function edit_role($role_id)
    {
        $this->render(
            'admin/role_permissions/edit',
            [
                'title' => 'Edit Role Permissions',
                'role_id' => $role_id,
                'roles' => $this->roleservice->getRoles(),
                'permissions' => $this->permissionservice->getPermissions(),
                'assigned_permissions' => $this->rolepermissionservice->getPermissionIdsByRole($role_id)
            ]
        );
    }

    public function update($role_id)
    {
        if (!$this->requestvalidator->validate('RolePermission', 'update')) {

            $this->render(
                'admin/role_permissions/edit',
                [
                    'title' => 'Edit Role Permissions',
                    'role_id' => $role_id,
                    'roles' => $this->roleservice->getRoles(),
                    'permissions' => $this->permissionservice->getPermissions(),
                    'assigned_permissions' => $this->rolepermissionservice->getPermissionIdsByRole($role_id),
                    'errors' => $this->form_validation->error_array()
                ]
            );

            return;
        }

        $this->rolepermissionservice->updatePermissions(
            $role_id,
            $this->input->post('permission_id')
        );

        redirect('admin/role_permissions');
    }

    public function delete_role($role_id)
    {
        $this->rolepermissionservice->deleteByRole($role_id);

        redirect('admin/role_permissions');
    }
}