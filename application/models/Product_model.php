<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Product_Model extends CI_Model
{

    protected $table = 'products';


  public function findAll($params = [])
{
    $this->db->select("
        products.*,
        users.name AS creator,
        status_lookup.value AS status_name,
        category_lookup.value AS category_name
    ");

    $this->db->from($this->table);

    $this->db->join(
        'users',
        'users.id = products.created_by',
        'left'
    );

    $this->db->join(
        'lookups AS status_lookup',
        'status_lookup.id = products.status_lookup_id',
        'left'
    );

    $this->db->join(
        'lookups AS category_lookup',
        'category_lookup.id = products.category_lookup_id',
        'left'
    );

    $this->db->where('products.deleted_at', NULL);

    if (!empty($params['keyword'])) {
        $this->db->group_start();

        $this->db->like(
            'products.name',
            $params['keyword']
        );

        $this->db->or_like(
            'products.sku',
            $params['keyword']
        );

        $this->db->group_end();
    }

    $this->db->order_by(
        'products.created_at',
        'DESC'
    );

    return $this->db->get()->result();
}



    public function find($id)
    {

        return $this->db
            ->where('id', $id)
            ->where(
                'deleted_at',
                NULL
            )
            ->get($this->table)
            ->row();

    }

    public function insert($data)
    {
        $this->db->insert(
            $this->table,
            $data
        );


        return $this->db->insert_id();

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


    public function softDelete($id)
    {

        return $this->db
            ->where('id', $id)
            ->update(
                $this->table,
                [
                    'deleted_at'
                    =>
                        date(
                            'Y-m-d H:i:s'
                        )
                ]
            );

    }

public function getLookupsByGroup($groupCode)
{
    return $this->db
        ->select('lookups.id, lookups.value')
        ->from('lookups')
        ->join(
            'lookup_groups',
            'lookup_groups.id = lookups.group_id'
        )
        ->where('lookup_groups.code', $groupCode)
        ->where('lookups.is_active', 1)
        ->order_by('lookups.sort_order', 'ASC')
        ->get()
        ->result();
}

}