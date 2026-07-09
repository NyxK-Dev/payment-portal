<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Role_model extends CI_Model
{

    protected $table = 'roles';



    public function getAll()
    {
        return $this->db
 
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





    public function existsName(
        $name,
        $ignoreId = null
    )
    {

        $this->db
            ->where('name',$name);



        if($ignoreId !== null)
        {

            $this->db
                ->where(
                    'id !=',
                    $ignoreId
                );

        }



        return $this->db
            ->count_all_results(
                $this->table
            ) > 0;

    }





    public function create($data)
    {

        return $this->db
            ->insert(
                $this->table,
                $data
            );

    }





    public function update(
        $id,
        $data
    )
    {

        return $this->db
            ->where(
                'id',
                $id
            )
            ->update(
                $this->table,
                $data
            );

    }





    public function delete($id)
    {

        return $this->db
            ->where(
                'id',
                $id
            )
            ->delete(
                $this->table
            );

    }


}