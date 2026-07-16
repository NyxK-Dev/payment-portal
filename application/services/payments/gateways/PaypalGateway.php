<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PaypalGateway implements PaymentGatewayInterface
{
    protected $paypalService;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->service('PaypalService');
        $this->paypalService = $CI->paypalservice;
    }

    public function name(): string
    {
        return 'paypal';
    }

    public function createPayment(array $order, array $payment, array $cart, string $idempotencyKey)
    {
        return $this->paypalService->createOrder($order, $payment);
    }

    public function capture(string $transactionId)
    {
        return $this->paypalService->captureOrder($transactionId);
    }

    public function refund(array $data)
    {
        // Implement refund via PaypalService if needed.
        return ['success' => false, 'message' => 'Not implemented'];
    }

    public function verifyWebhook(array $payload)
    {
        // Implement webhook verification if needed.
        return ['success' => false, 'message' => 'Not implemented'];
    }
}