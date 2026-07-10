<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Product_Model extends CI_Model
{

    protected $table = 'products';


    /**
     * Common Product Query
     */
    private function productQuery()
    {
        $this->db->select("
            products.*,
            users.name AS creator,
            category_lookup.value AS category_name,
            status_lookup.value AS status_name
        ");

        $this->db->from($this->table);

        $this->db->join(
            'users',
            'users.id = products.created_by',
            'left'
        );

        $this->db->join(
            'lookups AS category_lookup',
            'category_lookup.id = products.category_lookup_id',
            'left'
        );

        $this->db->join(
            'lookups AS status_lookup',
            'status_lookup.id = products.status_lookup_id',
            'left'
        );

        $this->db->where(
            'products.deleted_at',
            NULL
        );
    }



    /**
     * Apply Keyword Search
     */
    private function applySearch($keyword)
    {
        if (!empty($keyword)) {

            $this->db->group_start();

            $this->db->like(
                'products.name',
                $keyword
            );

            $this->db->or_like(
                'products.sku',
                $keyword
            );

            $this->db->group_end();
        }
    }



    /**
     * Get All Products
     */
    public function findAll($params = [])
    {
        $this->productQuery();


        $this->applySearch(
            $params['keyword'] ?? null
        );


        return $this->db
            ->order_by(
                'products.created_at',
                'DESC'
            )
            ->get()
            ->result();
    }



    /**
     * Find Product By ID
     */
    public function find($id)
    {
        $this->productQuery();


        return $this->db
            ->where(
                'products.id',
                $id
            )
            ->get()
            ->row();
    }



    /**
     * Insert Product
     */
    public function insert($data)
    {
        $this->db->insert(
            $this->table,
            $data
        );


        return $this->db->insert_id();
    }



    /**
     * Update Product
     */
    public function update($id, $data)
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



    /**
     * Soft Delete
     */
    public function softDelete($id)
    {
        return $this->update(
            $id,
            [
                'deleted_at' => date(
                    'Y-m-d H:i:s'
                )
            ]
        );
    }



    /**
     * Get Lookup By Group
     */
    public function getLookupsByGroup($groupCode)
    {
        return $this->db
            ->select(
                'lookups.id, lookups.value'
            )
            ->from('lookups')
            ->join(
                'lookup_groups',
                'lookup_groups.id = lookups.group_id'
            )
            ->where(
                'lookup_groups.code',
                $groupCode
            )
            ->where(
                'lookups.is_active',
                1
            )
            ->order_by(
                'lookups.sort_order',
                'ASC'
            )
            ->get()
            ->result();
    }



    /**
     * Get Active Products
     */
    public function getActiveProducts($keyword = null)
    {
        $this->productQuery();


        $this->db->where(
            'status_lookup.code',
            'ACTIVE'
        );


        $this->applySearch($keyword);


        return $this->db
            ->order_by(
                'products.created_at',
                'DESC'
            )
            ->get()
            ->result();
    }



    /**
     * Find Active Product
     */
    public function findActiveProduct($id)
    {
        $this->productQuery();


        $this->db->where(
            'products.id',
            $id
        );


        $this->db->where(
            'status_lookup.code',
            'ACTIVE'
        );


        return $this->db
            ->get()
            ->row();
    }

}