<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StripeWebhookEvent_model extends CI_Model
{
    protected $table = 'stripe_webhook_events';

    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    public function findByEventId($eventId)
    {
        return $this->db
            ->where('event_id', $eventId)
            ->get($this->table)
            ->row();
    }

    public function markProcessed($id)
    {
        return $this->db
            ->where('id', $id)
            ->update(
                $this->table,
                [
                    'processed' => 1,
                    'processed_at' => date('Y-m-d H:i:s'),
                    'processing_completed_at' => date('Y-m-d H:i:s')
                ]
            );
    }
}