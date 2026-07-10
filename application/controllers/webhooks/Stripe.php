<?php

defined('BASEPATH') OR exit('No direct script access allowed');


use Stripe\Exception\SignatureVerificationException;


class Stripe extends CI_Controller
{


    protected $stripeService;
    protected $paymentService;



    public function __construct()
    {
        parent::__construct();


        $this->load->service(
            'StripeService'
        );


        $this->load->service(
            'PaymentService'
        );


        $this->stripeService =
            $this->StripeService;


        $this->paymentService =
            $this->PaymentService;

    }




    public function index()
    {

        /**
         * Stripe payload
         */
        $payload =
            file_get_contents(
                'php://input'
            );


        $signature =
            $_SERVER[
                'HTTP_STRIPE_SIGNATURE'
            ];




        try
        {


            /**
             * Verify webhook
             */
            $event =
                $this->stripeService
                     ->constructWebhookEvent(
                        $payload,
                        $signature
                     );




        }
        catch(Exception $e)
        {


            http_response_code(
                400
            );


            echo $e->getMessage();

            return;

        }






        /**
         * Event ID
         */

        $eventId =
            $event->id;



        /**
         * Idempotency check
         */

        if(
            $this->paymentService
                ->webhookExists(
                    $eventId
                )
        )
        {

            http_response_code(
                200
            );

            return;

        }






        /**
         * Save webhook event
         */

        $webhookId =
            $this->paymentService
                 ->saveWebhookEvent(
                    $event
                 );






        try
        {


            switch(
                $event->type
            )
            {


                case
                'checkout.session.completed':


                    $this->paymentService
                         ->handleSuccessfulPayment(
                            $event
                         );

                    break;



                case
                'payment_intent.payment_failed':


                    $this->paymentService
                         ->handleFailedPayment(
                            $event
                         );


                    break;



                default:

                    log_message(
                        'info',
                        'Stripe event ignored: '
                        .$event->type
                    );

            }





            /**
             * Mark webhook processed
             */

            $this->paymentService
                 ->markWebhookProcessed(
                    $webhookId
                 );





            http_response_code(
                200
            );



        }
        catch(Exception $e)
        {


            log_message(
                'error',
                $e->getMessage()
            );


            http_response_code(
                500
            );


        }

    }


}