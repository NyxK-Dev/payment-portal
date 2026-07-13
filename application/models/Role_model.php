<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Role_model extends CI_Model
{
    protected $table = 'roles';

    public function roleQuery()
    {
        return $this->db
            ->select([
                'roles.id',
                'roles.name',
                'roles.description',
                'roles.created_at',
                'roles.updated_at'
            ])
            ->from($this->table);
    }

    public function getTable()
    {
        return $this->table;
    }
}