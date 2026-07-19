<?php

defined('BASEPATH') OR exit('No direct script access allowed');


interface PaymentGatewayInterface
{

    /**
     * Gateway name
     */
    public function name(): string;
    public function createPayment(array $order,array $payment,array $cart,string $idempotencyKey);
    public function capture(string $transactionId);
    public function refund(array $data);
    public function verifyWebhook(array $payload);

}