<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/RolePermissionRepositoryInterface.php';


class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    protected $CI;



    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Role_permission_model');
    }



    /**
     * Base query
     */
    private function query()
    {
        return $this->CI->db
            ->from(
                $this->table()
            );
    }



    /**
     * Table name
     */
    private function table()
    {
        return $this->CI
            ->Role_permission_model
            ->getTable();
    }



    /**
     * Get roles with permissions
     */
    public function getAll()
    {
        return $this->query()
            ->select([
                'roles.id AS role_id',
                'roles.name AS role_name',
                'GROUP_CONCAT(permissions.name) AS permissions'
            ])
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



    /**
     * Insert role permission
     */
    public function insert(array $data): bool
    {
        return $this->CI->db->insert(
            $this->table(),
            $data
        );
    }



    /**
     * Delete permissions by role
     */
    public function deleteByRole(int $roleId): bool
    {
        if ($roleId <= 0) {
            return false;
        }


        return $this->CI->db
            ->where(
                'role_id',
                $roleId
            )
            ->delete(
                $this->table()
            );
    }



    /**
     * Get permission ids
     */
    public function getPermissionIdsByRole(int $roleId): array
    {
        if ($roleId <= 0) {
            return [];
        }


        $rows = $this->query()
            ->select(
                'permission_id'
            )
            ->where(
                'role_id',
                $roleId
            )
            ->get()
            ->result_array();


        return array_column(
            $rows,
            'permission_id'
        );
    }



    /**
     * Check permission
     */
    public function hasPermission(
        int $roleId,
        string $permission
    ): bool {

        return $this->query()
            ->select(
                'permissions.id'
            )
            ->join(
                'permissions',
                'permissions.id = role_permissions.permission_id'
            )
            ->where(
                'role_permissions.role_id',
                $roleId
            )
            ->where(
                'permissions.code',
                trim($permission)
            )
            ->limit(1)
            ->count_all_results() > 0;
    }
}