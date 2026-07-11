<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class StripeWebhookEventRepository
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model(
            'StripeWebhookEvent_model'
        );
    }

    public function create(array $data)
    {
        return $this->CI->StripeWebhookEvent_model
            ->insert($data);
    }

    public function find($id)
    {
        return $this->CI->StripeWebhookEvent_model
            ->find($id);
    }

    public function findByEventId($eventId)
    {
        return $this->CI->StripeWebhookEvent_model
            ->findByEventId($eventId);
    }

    public function markProcessed($id)
    {
        return $this->CI->StripeWebhookEvent_model
            ->markProcessed($id);
    }
}