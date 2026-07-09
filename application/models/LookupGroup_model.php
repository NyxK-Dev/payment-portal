<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class LookupGroup_model extends CI_Model
{

    protected $table = 'lookup_groups';
    public function getAll()
    {
        return $this->db
            ->order_by('id','DESC')
            ->get($this->table)
            ->result();
    }


    public function find($id)
    {
        return $this->db
            ->where('id',$id)
            ->get($this->table)
            ->row();
    }


    public function create($data)
    {
        $this->db->insert(
            $this->table,
            $data
        );

        return $this->db->insert_id();
    }


    public function update($id,$data)
    {
        return $this->db
            ->where('id',$id)
            ->update(
                $this->table,
                $data
            );
    }

}