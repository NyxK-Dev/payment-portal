<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/StripeTransactionInterface.php';

class StripeTransactionRepository implements StripeTransactionInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('StripeTransaction_model');

        $this->table = $this->CI->StripeTransaction_model->getTable();
    }

    /**
     * Create Stripe Transaction
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
     * Find Transaction By ID
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
     * Find Transactions By Payment ID
     */
    public function findByPaymentId($paymentId)
    {
        return $this->CI->db
            ->where(
                'payment_id',
                $paymentId
            )
            ->get($this->table)
            ->result();
    }

    /**
     * Find Transaction By Payment Intent ID
     */
    public function findByPaymentIntent($paymentIntentId)
    {
        return $this->CI->db
            ->where(
                'payment_intent_id',
                $paymentIntentId
            )
            ->get($this->table)
            ->row();
    }
}