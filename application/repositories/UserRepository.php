<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserRepository
{
    protected $CI;

    protected $table = 'users';


    public function __construct()
    {
        $this->CI = &get_instance();
    }


    public function findById($id)
    {
        return $this->CI->db
            ->select('users.*, roles.name AS role_name')
            ->from($this->table)
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $id)
            ->get()
            ->row();
    }

    public function findByEmail($email)
    {
        return $this->CI->db
            ->select('users.*, roles.name AS role_name')
            ->from($this->table)
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.email', $email)
            ->get()
            ->row();
    }
    public function getAll($limit = 20, $offset = 0)
    {

        $users = $this->CI->db
            ->select('
                users.id,
                users.name,
                users.email,
                users.role_id,
                users.status_lookup_id,
                users.created_at,
                roles.name AS role_name
            ')
            ->from($this->table)
            ->join(
                'roles',
                'roles.id = users.role_id',
                'left'
            )
            ->where(
                'users.deleted_at',
                NULL
            )
            ->limit(
                $limit,
                $offset
            )
            ->get()
            ->result_array();



        $result = [];


        foreach ($users as $user) {

            $result[] = new User_model($user);
        }


        return $result;
    }



    public function find($id)
    {

        $user = $this->CI->db
            ->select('
                users.id,
                users.name,
                users.email,
                users.role_id,
                users.status_lookup_id,
                users.created_at,
                roles.name AS role_name
            ')
            ->from($this->table)
            ->join(
                'roles',
                'roles.id = users.role_id',
                'left'
            )
            ->where(
                'users.id',
                $id
            )
            ->get()
            ->row_array();



        if (!$user) {
            return null;
        }


        return $user;
    }



    public function create(array $data)
    {

        $this->CI->db->insert(
            $this->table,
            $data
        );


        return $this->CI->db->insert_id();
    }



    public function update($id, array $data)
    {

        return $this->CI->db
            ->where(
                'id',
                $id
            )
            ->update(
                $this->table,
                $data
            );
    }


    
    public function getRoleByName($name)
    {
        return $this->CI->db
            ->where('name', $name)
            ->get('roles')
            ->row();
    }

    public function updateRole($id, $roleId)
    {

        return $this->update(
            $id,
            [
                'role_id' => $roleId,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }
       public function updateLastLogin($id)
    {
        return $this->update($id, array(
            'last_login_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }
}
