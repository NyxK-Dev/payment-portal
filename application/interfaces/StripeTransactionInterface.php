<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface StripeTransactionInterface
{
    /**
     * Create Stripe Transaction
     */
    public function create(array $data);

    /**
     * Find Transaction By ID
     */
    public function find($id);

    /**
     * Find Transactions By Payment ID
     */
    public function findByPaymentId($paymentId);

    /**
     * Find Transaction By Payment Intent ID
     */
    public function findByPaymentIntent($paymentIntentId);
}