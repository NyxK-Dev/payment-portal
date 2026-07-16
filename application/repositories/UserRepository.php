<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/UserRepositoryInterface.php';


class UserRepository implements UserRepositoryInterface
{
    protected $CI;


    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('User_model');
    }


    private function userQuery()
    {
        return $this->CI->db
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.password',
                'users.role_id',
                'users.status_lookup_id',
                'users.created_at',
                'users.updated_at',
                'users.deleted_at',
                'users.last_login_at',
                'roles.name AS role_name'
            ])
            ->from($this->table())
            ->join(
                'roles',
                'roles.id = users.role_id',
                'left'
            )
            ->where(
                'users.deleted_at',
                null
            );
    }


    private function table()
    {
        return $this->CI
            ->User_model
            ->getTable();
    }


    private function now()
    {
        return date('Y-m-d H:i:s');
    }


    public function findById(int $id)
    {
        return $this->userQuery()
            ->where(
                'users.id',
                $id
            )
            ->limit(1)
            ->get()
            ->row();
    }


    public function findByEmail(string $email)
    {
        return $this->userQuery()
            ->where(
                'users.email',
                strtolower(trim($email))
            )
            ->limit(1)
            ->get()
            ->row();
    }


    public function getAll(int $limit = 20, int $offset = 0)
    {
        return $this->userQuery()
            ->order_by(
                'users.id',
                'ASC'
            )
            ->limit(
                $limit,
                $offset
            )
            ->get()
            ->result();
    }


    public function find(int $id)
    {
        return $this->findById($id);
    }


    public function create(array $data): int
    {
        $data['created_at'] = $this->now();

        $this->CI->db->insert(
            $this->table(),
            $data
        );

        return (int) $this->CI->db->insert_id();
    }


    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = $this->now();

        return $this->CI->db
            ->where(
                'id',
                $id
            )
            ->where(
                'deleted_at',
                null
            )
            ->update(
                $this->table(),
                $data
            );
    }


    public function getRoleByName(string $name)
    {
        return $this->CI->db
            ->where(
                'name',
                trim($name)
            )
            ->limit(1)
            ->get('roles')
            ->row();
    }


    public function updateRole(int $id, int $roleId): bool
    {
        return $this->update(
            $id,
            [
                'role_id' => $roleId
            ]
        );
    }


    public function updateLastLogin(int $id): bool
    {
        return $this->update(
            $id,
            [
                'last_login_at' => $this->now()
            ]
        );
    }
}