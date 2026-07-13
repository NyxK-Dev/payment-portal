<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/ProductInterface.php';

class ProductRepository implements ProductInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Product_Model');

        $this->table = $this->CI->Product_Model->getTable();
    }

    /**
     * Base Product Query
     */
    private function productQuery()
    {
        $this->CI->db->select("
            products.*,
            users.name AS creator,
            category_lookup.value AS category_name,
            status_lookup.value AS status_name
        ");

        $this->CI->db->from($this->table);

        $this->CI->db->join(
            'users',
            'users.id = products.created_by',
            'left'
        );

        $this->CI->db->join(
            'lookups AS category_lookup',
            'category_lookup.id = products.category_lookup_id',
            'left'
        );

        $this->CI->db->join(
            'lookups AS status_lookup',
            'status_lookup.id = products.status_lookup_id',
            'left'
        );

        $this->CI->db->where(
            'products.deleted_at',
            null
        );
    }

    /**
     * Apply Search
     */
    private function applySearch($keyword)
    {
        if (!empty($keyword)) {

            $this->CI->db->group_start();

            $this->CI->db->like(
                'products.name',
                $keyword
            );

            $this->CI->db->or_like(
                'products.sku',
                $keyword
            );

            $this->CI->db->group_end();
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

        return $this->CI->db
            ->order_by(
                'products.created_at',
                'DESC'
            )
            ->get()
            ->result();
    }

    /**
     * Find Product
     */
    public function find($id)
    {
        $this->productQuery();

        return $this->CI->db
            ->where(
                'products.id',
                $id
            )
            ->get()
            ->row();
    }

    /**
     * Create Product
     */
    public function insert($data)
    {
        $this->CI->db->insert(
            $this->table,
            $data
        );

        return $this->CI->db->insert_id();
    }

    /**
     * Update Product
     */
    public function update($id, $data)
    {
        return $this->CI->db
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
     * Soft Delete Product
     */
    public function softDelete($id)
    {
        return $this->update(
            $id,
            [
                'deleted_at' => date('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * Lookup List
     */
    public function getLookupsByGroup($groupCode)
    {
        return $this->CI->db
            ->select('lookups.id, lookups.value')
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
     * Active Products
     */
    public function getActiveProducts($keyword = null)
    {
        $this->productQuery();

        $this->CI->db->where(
            'status_lookup.code',
            'ACTIVE'
        );

        $this->applySearch($keyword);

        return $this->CI->db
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

        $this->CI->db->where(
            'products.id',
            $id
        );

        $this->CI->db->where(
            'status_lookup.code',
            'ACTIVE'
        );

        return $this->CI->db
            ->get()
            ->row();
    }

    /**
     * Reduce Stock
     */
    public function decreaseStock($productId, $quantity)
    {
        $this->CI->db->set(
            'stock_qty',
            'stock_qty - ' . (int)$quantity,
            false
        );

        $this->CI->db->where(
            'id',
            $productId
        );

        $this->CI->db->where(
            'stock_qty >=',
            $quantity
        );

        return $this->CI->db->update(
            $this->table
        );
    }
}