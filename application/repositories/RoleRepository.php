<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';


class RoleRepository implements RoleRepositoryInterface
{
    protected $CI;


    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('Role_model');
    }


    private function roleQuery()
    {
        return $this->CI->db
            ->select([
                'roles.id',
                'roles.name',
                'roles.description',
                'roles.created_at',
                'roles.updated_at'
            ])
            ->from($this->table());
    }



    private function table()
    {
        return $this->CI
            ->Role_model
            ->getTable();
    }



    public function getAll(): array
    {
        return $this->roleQuery()
            ->order_by(
                'roles.name',
                'ASC'
            )
            ->get()
            ->result();
    }



    public function find(int $id)
    {
        if ($id <= 0) {
            return null;
        }


        return $this->roleQuery()
            ->where(
                'roles.id',
                $id
            )
            ->limit(1)
            ->get()
            ->row();
    }



    public function existsName(
        string $name,
        ?int $ignoreId = null
    ): bool {

        $query = $this->roleQuery()
            ->where(
                'roles.name',
                trim($name)
            );


        if ($ignoreId !== null) {

            $query->where(
                'roles.id !=',
                $ignoreId
            );
        }


        return $query
            ->count_all_results() > 0;
    }



    public function create(array $data): int
    {
        $this->CI->db->trans_start();


        $this->CI->db->insert(
            $this->table(),
            $data
        );


        $id = (int)
            $this->CI->db->insert_id();


        $this->CI->db->trans_complete();


        if (!$this->CI->db->trans_status()) {
            return 0;
        }


        return $id;
    }



    public function update(
        int $id,
        array $data
    ): bool {

        if ($id <= 0) {
            return false;
        }


        $this->CI->db->trans_start();


        $this->CI->db
            ->where(
                'id',
                $id
            )
            ->update(
                $this->table(),
                $data
            );


        $this->CI->db->trans_complete();


        return $this->CI->db->trans_status();
    }



    public function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }


        $this->CI->db->trans_start();


        // Delete related permissions
        $this->CI->db
            ->where(
                'role_id',
                $id
            )
            ->delete(
                'role_permissions'
            );


        // Delete role
        $this->CI->db
            ->where(
                'id',
                $id
            )
            ->delete(
                $this->table()
            );


        $this->CI->db->trans_complete();


        return $this->CI->db->trans_status();
    }
}