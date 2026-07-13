<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permission_model extends CI_Model
{
    protected $table = 'permissions';

    /**
     * Base query
     */
    public function query()
    {
        return $this->db
            ->select([
                'permissions.id',
                'permissions.name',
                'permissions.code',
                'permissions.description',
                'permissions.created_at',
                'permissions.updated_at'
            ])
            ->from($this->table);
    }

    /**
     * Get table name
     */
    public function getTable()
    {
        return $this->table;
    }
}