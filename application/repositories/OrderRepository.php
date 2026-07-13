<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'interfaces/OrderInterface.php';

class OrderRepository implements OrderInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Order_model');

        $this->table = $this->CI->Order_model->getTable();
    }

    /**
     * Create Order
     */
    public function create(array $data)
    {
        $this->CI->db->insert(
            $this->table,
            $data
        );

        return $this->CI->db->insert_id();
    }

    /**
     * Find Order
     */
    public function find($id)
    {
        return $this->CI->db
            ->where(
                'id',
                $id
            )
            ->get($this->table)
            ->row();
    }

    /**
     * Find By Order Number
     */
    public function findByOrderNo($orderNo)
    {
        return $this->CI->db
            ->where(
                'order_no',
                $orderNo
            )
            ->get($this->table)
            ->row();
    }

    /**
     * Update Order
     */
    public function update($id, array $data)
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
     * Orders By Customer
     */
    public function getByUser($userId, $filters = [])
    {
        $this->CI->db
            ->select('orders.*')
            ->from($this->table)
            ->where(
                'orders.user_id',
                $userId
            );

        if (!empty($filters['keyword'])) {

            $this->CI->db->like(
                'orders.order_no',
                $filters['keyword']
            );
        }

        if (!empty($filters['from'])) {

            $this->CI->db->where(
                'DATE(orders.created_at) >=',
                $filters['from']
            );
        }

        if (!empty($filters['to'])) {

            $this->CI->db->where(
                'DATE(orders.created_at) <=',
                $filters['to']
            );
        }

        return $this->CI->db
            ->order_by(
                'orders.created_at',
                'DESC'
            )
            ->get()
            ->result();
    }

    /**
     * All Orders
     */
    public function getAll()
    {
        return $this->CI->db
            ->select([
                'orders.id',
                'orders.order_no',
                'orders.user_id',
                'orders.total_amount',
                'orders.status_lookup_id',
                'orders.created_at',
                'users.name AS customer_name',
                'lookups.value AS status_name'
            ])
            ->from($this->table)
            ->join(
                'users',
                'users.id = orders.user_id',
                'left'
            )
            ->join(
                'lookups',
                'lookups.id = orders.status_lookup_id',
                'left'
            )
            ->order_by(
                'orders.created_at',
                'DESC'
            )
            ->get()
            ->result();
    }

    /**
     * Order Details
     */
    public function findWithItems($id)
    {
        return $this->CI->db
            ->select([
                'orders.id',
                'orders.user_id',
                'orders.order_no',
                'orders.total_amount',
                'orders.status_lookup_id',
                'orders.created_at',
                'users.name AS customer_name',
                'users.email',
                'lookups.value AS status_name'
            ])
            ->from($this->table)
            ->join(
                'users',
                'users.id = orders.user_id',
                'left'
            )
            ->join(
                'lookups',
                'lookups.id = orders.status_lookup_id',
                'left'
            )
            ->where(
                'orders.id',
                $id
            )
            ->limit(1)
            ->get()
            ->row();
    }
}