<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/PaymentEventInterface.php';

class PaymentEventRepository implements PaymentEventInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Payment_event_model');

        $this->table = $this->CI->Payment_event_model->getTable();
    }

    /**
     * Create Payment Event
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
     * Get Payment Events By Payment ID
     */
    public function findByPaymentId($paymentId)
    {
        return $this->CI->db
            ->where(
                'payment_id',
                $paymentId
            )
            ->get($this->table)
            ->result_array();
    }
}