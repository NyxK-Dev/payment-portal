<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permission_model extends CI_Model
{
    protected $table = 'permissions';



    public function getAll()
    {

        return $this->db
            ->order_by('code')
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





    /*
    |--------------------------------------------------------------------------
    | CHANGE START
    |
    | Get permission by code
    |
    | Example:
    |
    | manage_invoices
    | view_own_invoices
    |
    |--------------------------------------------------------------------------
    */

    public function getByCode($code)
    {

        return $this->db
            ->where('code',
                $code
            )
            ->get(
                $this->table
            )
            ->row();
    }


    /*
    |--------------------------------------------------------------------------
    | CHANGE END
    |--------------------------------------------------------------------------
    */





    public function existsCode(
        $code,
        $ignoreId = null
    ) {


        $this->db
            ->where(
                'code',
                $code
            );


        if ($ignoreId) {


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
    ) {

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







    /*
    |--------------------------------------------------------------------------
    | Check permission exists
    |--------------------------------------------------------------------------
    */

    public function exists($code)
    {

        return $this->db
            ->where(
                'code',
                $code
            )
            ->count_all_results(
                $this->table
            ) > 0;
    }
    /**
     * Get table name
     */
    public function getTable()
    {
        return $this->table;
    }
}
