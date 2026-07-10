<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RolePermissionService
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->repository(
            'RolePermissionRepository'
        );
    }

    public function getAll()
    {
        return $this->CI
            ->rolepermissionrepository
            ->getAll();
    }

    public function assignPermissions($role_id, $permission_ids)
    {
        $this->CI
            ->rolepermissionrepository
            ->deleteByRole($role_id);

        if (empty($permission_ids)) {
            return;
        }

        foreach ($permission_ids as $permission_id) {

            $this->CI
                ->rolepermissionrepository
                ->insert([
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                ]);
        }
    }

    public function getPermissionIdsByRole($role_id)
    {
        return $this->CI
            ->rolepermissionrepository
            ->getPermissionIdsByRole($role_id);
    }

    public function updatePermissions($role_id, $permission_ids)
    {
        $this->CI
            ->rolepermissionrepository
            ->deleteByRole($role_id);

        if (empty($permission_ids)) {
            return;
        }

        foreach ($permission_ids as $permission_id) {

            $this->CI
                ->rolepermissionrepository
                ->insert([
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                ]);
        }
    }

    public function deleteByRole($role_id)
    {
        return $this->CI
            ->rolepermissionrepository
            ->deleteByRole($role_id);
    }

    public function hasPermission($role_id, $permission)
    {
        return $this->CI
            ->rolepermissionrepository
            ->hasPermission(
                $role_id,
                $permission
            );
    }
}
