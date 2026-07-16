<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/PaymentGatewayInterface.php';
require_once APPPATH . 'interfaces/PaymentGatewayResolverInterface.php';

class PaymentGatewayResolver implements PaymentGatewayResolverInterface
{
    /**
     * Registered gateways
     *
     * @var PaymentGatewayInterface[]
     */
    protected $gateways = [];
    public function __construct()
    {
        require_once APPPATH.'services/payments/gateways/StripeGateway.php';
        require_once APPPATH.'services/payments/gateways/PaypalGateway.php';

        $this->register(new StripeGateway());
        $this->register(new PaypalGateway());
    }

    /**
     * Register gateway
     */
    public function register(PaymentGatewayInterface $gateway)
    {
        $this->gateways[$gateway->name()] = $gateway;
    }

    /**
     * Resolve gateway
     */
    public function resolve(string $gateway): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$gateway])) {
            throw new Exception('Payment gateway not supported: ' . $gateway);
        }

        return $this->gateways[$gateway];
    }
}