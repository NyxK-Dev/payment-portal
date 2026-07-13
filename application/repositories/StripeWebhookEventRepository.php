<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/StripeWebhookEventInterface.php';

class StripeWebhookEventRepository implements StripeWebhookEventInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Stripe_webhook_event_model');

        $this->table = $this->CI->Stripe_webhook_event_model->getTable();
    }

    /**
     * Check if Event Already Exists
     */
    public function existsByEventId($eventId)
    {
        return $this->CI->db
            ->where(
                'event_id',
                $eventId
            )
            ->from($this->table)
            ->count_all_results() > 0;
    }

    /**
     * Create Webhook Event
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
     * Update Webhook Event
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