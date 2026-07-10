<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Role_permission_model extends CI_Model
{

    protected $table = 'role_permissions';

    public function getAll()
    {

        return $this->db
            ->select('
                roles.id as role_id,
                roles.name as role_name,
                GROUP_CONCAT(permissions.name) as permissions
            ')
            ->from($this->table)

            ->join(
                'roles',
                'roles.id = role_permissions.role_id'
            )

            ->join(
                'permissions',
                'permissions.id = role_permissions.permission_id'
            )

            ->group_by(
                'roles.id'
            )

            ->get()
            ->result();
    }


    public function insert($data)
    {

        return $this->db
            ->insert(
                $this->table,
                $data
            );
    }

    public function deleteByRole($role_id)
    {

        return $this->db
            ->where(
                'role_id',
                $role_id
            )
            ->delete(
                $this->table
            );
    }

    public function getPermissionIdsByRole($role_id)
    {

        $result = $this->db
            ->select('permission_id')
            ->where(
                'role_id',
                $role_id
            )
            ->get($this->table)
            ->result();



        return array_column(
            $result,
            'permission_id'
        );
    }


    public function hasPermission($role_id, $permission)
    {
        return $this->db
            ->select('permissions.id')
            ->from('role_permissions')
            ->join(
                'permissions',
                'permissions.id = role_permissions.permission_id'
            )
            ->where(
                'role_permissions.role_id',
                $role_id
            )
            ->where(
                'permissions.code',
                $permission
            )
            ->count_all_results() > 0;
    }
}
