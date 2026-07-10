<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Permission_model extends CI_Model
{

    protected $table = 'permissions';


    public function getAll()
    {
        return $this->db
            ->get($this->table)
            ->result();
    }



    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }



    public function existsCode($code, $ignoreId = null)
    {

        $this->db
            ->where('code', $code);


        if ($ignoreId) {
            $this->db
                ->where('id !=', $ignoreId);
        }


        return $this->db
            ->count_all_results($this->table) > 0;
    }



    public function create($data)
    {
        return $this->db
            ->insert(
                $this->table,
                $data
            );
    }



    public function update($id, $data)
    {

        return $this->db
            ->where('id', $id)
            ->update(
                $this->table,
                $data
            );
    }

    public function delete($id)
    {

        return $this->db
            ->where('id', $id)
            ->delete(
                $this->table
            );
    }

    public function exists($code)
    {
        return $this->db
            ->where('code', $code)
            ->count_all_results('permissions') > 0;
    }
}
