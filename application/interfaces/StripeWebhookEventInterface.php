<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface StripeWebhookEventInterface
{
    /**
     * Check if Stripe Event Exists
     */
    public function existsByEventId($eventId);

    /**
     * Create Webhook Event
     */
    public function create(array $data);

    /**
     * Update Webhook Event
     */
    public function update($id, array $data);
}