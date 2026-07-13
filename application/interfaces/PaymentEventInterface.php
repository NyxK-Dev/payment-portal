<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface PaymentEventInterface
{
    /**
     * Create Payment Event
     */
    public function create(array $data);

    /**
     * Get Payment Events By Payment ID
     */
    public function findByPaymentId($paymentId);
}