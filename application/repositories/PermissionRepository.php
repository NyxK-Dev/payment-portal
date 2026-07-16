<?php

defined('BASEPATH') or exit('No direct script access allowed');


require_once APPPATH . 'interfaces/PermissionRepositoryInterface.php';


class PermissionRepository implements PermissionRepositoryInterface
{
    protected $CI;



    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Permission_model');
    }



    /**
     * Base Query
     */
    private function query()
    {
        return $this->CI->db
            ->select([
                'permissions.id',
                'permissions.name',
                'permissions.code',
                'permissions.description',
                'permissions.created_at',
                'permissions.updated_at'
            ])
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
            ->Permission_model
            ->getTable();
    }



    /**
     * Get all permissions
     */
    public function getAll(): array
    {
        return $this->query()
            ->order_by(
                'permissions.id',
                'ASC'
            )
            ->get()
            ->result();
    }



    /**
     * Find permission
     */
    public function find(int $id)
    {
        if ($id <= 0) {
            return null;
        }


        return $this->query()
            ->where(
                'permissions.id',
                $id
            )
            ->limit(1)
            ->get()
            ->row();
    }



    /**
     * Find by code
     */
    public function getByCode(string $code)
    {
        return $this->query()
            ->where(
                'permissions.code',
                trim($code)
            )
            ->limit(1)
            ->get()
            ->row();
    }



    /**
     * Check duplicate code
     */
    public function existsCode(
        string $code,
        ?int $ignoreId = null
    ): bool {

        $query = $this->query()
            ->where(
                'permissions.code',
                trim($code)
            );


        if ($ignoreId !== null) {

            $query->where(
                'permissions.id !=',
                $ignoreId
            );
        }


        return $query
            ->count_all_results() > 0;
    }



    /**
     * Create
     */
    public function create(array $data): int
    {
        $this->CI->db->insert(
            $this->table(),
            $data
        );


        return (int)
            $this->CI->db->insert_id();
    }



    /**
     * Update
     */
    public function update(
        int $id,
        array $data
    ): bool {

        if ($id <= 0) {
            return false;
        }


        return $this->CI->db
            ->where(
                'id',
                $id
            )
            ->update(
                $this->table(),
                $data
            );
    }



    /**
     * Delete
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }


        return $this->CI->db
            ->where(
                'id',
                $id
            )
            ->delete(
                $this->table()
            );
    }



    /**
     * Exists
     */
    public function exists(string $code): bool
    {
        return $this->CI->db
            ->where(
                'code',
                trim($code)
            )
            ->count_all_results(
                $this->table()
            ) > 0;
    }
}