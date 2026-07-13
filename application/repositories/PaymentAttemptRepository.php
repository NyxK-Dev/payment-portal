<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/PaymentAttemptInterface.php';

class PaymentAttemptRepository implements PaymentAttemptInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('PaymentAttempt_model');

        $this->table = $this->CI->PaymentAttempt_model->getTable();
    }

    /**
     * Create Payment Attempt
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
     * Update Payment Attempt
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
     * Find Payment Attempt
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
     * Find By Stripe Session ID
     */
    public function findBySessionId($sessionId)
    {
        return $this->CI->db
            ->where(
                'stripe_session_id',
                $sessionId
            )
            ->get($this->table)
            ->row();
    }

    /**
     * Get Latest Attempt
     */
    public function getLatestAttempt($paymentId)
    {
        return $this->CI->db
            ->where(
                'payment_id',
                $paymentId
            )
            ->order_by(
                'attempt_no',
                'DESC'
            )
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    /**
     * Find Latest Attempt By Payment ID
     */
    public function findByPaymentId($paymentId)
    {
        return $this->CI->db
            ->where(
                'payment_id',
                $paymentId
            )
            ->order_by(
                'attempt_no',
                'DESC'
            )
            ->limit(1)
            ->get($this->table)
            ->row();
    }
}