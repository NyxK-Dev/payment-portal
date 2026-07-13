<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe_webhook_event_model extends CI_Model
{
    /**
     * Stripe Webhook Events Table
     */
    protected $table = 'stripe_webhook_events';

    /**
     * Get Table Name
     */
    public function getTable()
    {
        return $this->table;
    }
}