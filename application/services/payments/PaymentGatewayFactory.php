<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class PaymentGatewayFactory
{

    public function make(
        string $method
    )
    {


        switch($method)
        {


            case 'stripe':

                return new StripeGateway();



            case 'paypal':

                return new PaypalGateway();



            default:

                throw new Exception(
                    'Unsupported payment gateway'
                );

        }


    }


}