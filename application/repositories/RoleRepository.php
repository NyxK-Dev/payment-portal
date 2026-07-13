<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @var CI_Controller
     */
    protected $CI;


    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Role_model');
    }


    /**
     * Base query
     */
    private function query()
    {
        return $this->CI
            ->Role_model
            ->roleQuery();
    }


    /**
     * Table name
     */
    private function table()
    {
        return $this->CI
            ->Role_model
            ->getTable();
    }


    /**
     * Get all roles
     */
    public function getAll(): array
    {
        return $this->query()
            ->order_by('roles.name', 'ASC')
            ->get()
            ->result();
    }


    /**
     * Find role by id
     */
    public function find(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        return $this->query()
            ->where('roles.id', $id)
            ->limit(1)
            ->get()
            ->row();
    }


    /**
     * Check duplicate role name
     */
    public function existsName(string $name, ?int $ignoreId = null): bool
    {
        $query = $this->query()
            ->where('roles.name', trim($name));


        if ($ignoreId !== null) {
            $query->where('roles.id !=', $ignoreId);
        }


        return $query
            ->count_all_results() > 0;
    }


    /**
     * Create role
     */
    public function create(array $data): int
    {
        $this->CI->db->trans_start();

        $this->CI->db->insert(
            $this->table(),
            $data
        );

        $id = (int) $this->CI->db->insert_id();

        $this->CI->db->trans_complete();


        if (!$this->CI->db->trans_status()) {
            return 0;
        }


        return $id;
    }


    /**
     * Update role
     */
    public function update(int $id, array $data): bool
    {
        if ($id <= 0) {
            return false;
        }


        $this->CI->db->trans_start();

        $this->CI->db
            ->where('id', $id)
            ->update(
                $this->table(),
                $data
            );

        $this->CI->db->trans_complete();


        return $this->CI->db->trans_status();
    }


    /**
     * Delete role
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }


        $this->CI->db->trans_start();


        // Remove related permissions first
        $this->CI->db
            ->where('role_id', $id)
            ->delete('role_permissions');


        // Remove role
        $this->CI->db
            ->where('id', $id)
            ->delete(
                $this->table()
            );


        $this->CI->db->trans_complete();


        return $this->CI->db->trans_status();
    }
}