<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lookup_model extends CI_Model
{
    protected $table = 'lookups';


    public function getByGroup($groupId)
    {
        return $this->db
            ->select('
            lookups.*,
            lookup_groups.name AS group_name
        ')
            ->from($this->table)
            ->join(
                'lookup_groups',
                'lookup_groups.id = lookups.group_id',
                'left'
            )
            ->where(
                'lookups.group_id',
                $groupId
            )
            ->order_by(
                'lookups.sort_order',
                'ASC'
            )
            ->order_by(
                'lookups.value',
                'ASC'
            )
            ->get()
            ->result();
    }

    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }


    public function create($data)
    {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }


    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }


    public function delete($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete($this->table);
    }


    public function getAllWithGroup()
    {
        return $this->db
            ->select('lookups.*, lookup_groups.name as group_name')
            ->from($this->table)
            ->join(
                'lookup_groups',
                'lookup_groups.id = lookups.group_id',
                'left'
            )
            ->order_by('lookup_groups.name', 'ASC')
            ->order_by('lookups.value', 'ASC')
            ->get()
            ->result();
    }
    public function countByGroup($groupId)
    {
        return $this->db
            ->where('group_id', $groupId)
            ->count_all_results($this->table);
    }
}
