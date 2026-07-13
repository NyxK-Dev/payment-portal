<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface PaymentAttemptInterface
{
    /**
     * Create Payment Attempt
     */
    public function create(array $data);

    /**
     * Update Payment Attempt
     */
    public function update($id, array $data);

    /**
     * Find Payment Attempt By ID
     */
    public function find($id);

    /**
     * Find By Stripe Session ID
     */
    public function findBySessionId($sessionId);

    /**
     * Get Latest Attempt
     */
    public function getLatestAttempt($paymentId);

    /**
     * Find Latest Attempt By Payment ID
     */
    public function findByPaymentId($paymentId);
}