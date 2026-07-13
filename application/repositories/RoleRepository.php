<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

class RoleRepository implements RoleRepositoryInterface
{
    protected $CI;

    protected $table = 'roles';

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function getAll(): array
    {
        return $this->CI->db
            ->order_by('name', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function find(int $id)
    {
        return $this->CI->db
            ->where('id', $id)
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    public function existsName(string $name, ?int $ignoreId = null): bool
    {
        $this->CI->db
            ->where('name', trim($name));

        if ($ignoreId !== null) {
            $this->CI->db
                ->where('id !=', $ignoreId);
        }

        return $this->CI->db
            ->count_all_results($this->table) > 0;
    }

    public function create(array $data): int
    {
        $this->CI->db->insert(
            $this->table,
            $data
        );

        return (int) $this->CI->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        return $this->CI->db
            ->where('id', $id)
            ->update(
                $this->table,
                $data
            );
    }

    public function delete(int $id): bool
    {
        $this->CI->db->trans_start();

        $this->CI->db
            ->where('role_id', $id)
            ->delete('role_permissions');

        $this->CI->db
            ->where('id', $id)
            ->delete($this->table);

        $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }
}