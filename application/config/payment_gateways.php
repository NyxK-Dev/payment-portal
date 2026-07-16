<?php


$CI =& get_instance();
$CI->load->service('PaymentGatewayResolver');
$CI->paymentgatewayresolver->register(new StripeGateway());
$CI->paymentgatewayresolver->register(new PaypalGateway());