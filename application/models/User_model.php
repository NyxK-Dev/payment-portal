<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function userQuery()
    {
        return $this->db
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
            ->from($this->table)
            ->join(
                'roles',
                'roles.id = users.role_id',
                'left'
            )
            ->where('users.deleted_at', null);
    }

    public function getTable()
    {
        return $this->table;
    }
}