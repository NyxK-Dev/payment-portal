<?php

defined('BASEPATH') OR exit('No direct script access allowed');


interface PaymentGatewayResolverInterface
{

    public function resolve(
        string $gateway
    ): PaymentGatewayInterface;

}