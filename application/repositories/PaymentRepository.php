<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/PaymentInterface.php';

class PaymentRepository implements PaymentInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Payment_model');

        $this->table = $this->CI->Payment_model->getTable();
    }

    /**
     * Create Payment
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
     * Find Payment
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
     * Find Payment By Order ID
     */
    public function findByOrderId($orderId)
    {
        return $this->CI->db
            ->where(
                'order_id',
                $orderId
            )
            ->get($this->table)
            ->row();
    }

    /**
     * Update Payment
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
}