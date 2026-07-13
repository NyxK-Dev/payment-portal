<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/PermissionRepositoryInterface.php';

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * @var CI_Controller
     */
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Permission_model');
    }

    /**
     * Base query
     */
    private function query()
    {
        return $this->CI
            ->Permission_model
            ->query();
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
    public function getAll()
    {
        return $this->query()
            ->order_by('permissions.id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Find permission by id
     */
    public function find(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        return $this->query()
            ->where('permissions.id', $id)
            ->limit(1)
            ->get()
            ->row();
    }

    /**
     * Check duplicate code
     */
    public function existsCode(string $code, int $ignoreId = null): bool
    {
        $query = $this->query()
            ->where('permissions.code', trim($code));

        if ($ignoreId !== null) {
            $query->where('permissions.id !=', $ignoreId);
        }

        return $query
            ->count_all_results() > 0;
    }

    /**
     * Create permission
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
     * Update permission
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
     * Delete permission
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        $this->CI->db->trans_start();

        $this->CI->db
            ->where('id', $id)
            ->delete(
                $this->table()
            );

        $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }
}