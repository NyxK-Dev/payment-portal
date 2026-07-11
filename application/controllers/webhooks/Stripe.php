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
            $this->stripeservice;


        $this->paymentService =
            $this->paymentservice;

    }




public function index()
{
    log_message(
        'error',
        'WEBHOOK RECEIVED'
    );


    $payload = file_get_contents(
        'php://input'
    );


    if(!isset($_SERVER['HTTP_STRIPE_SIGNATURE']))
    {
        http_response_code(400);
        echo 'Missing Stripe Signature';
        return;
    }


    try
    {

        $event =
            $this->stripeService
                 ->constructWebhookEvent(
                    $payload,
                    $_SERVER['HTTP_STRIPE_SIGNATURE']
                 );


    }
    catch(\Exception $e)
    {

        http_response_code(400);

        log_message(
            'error',
            $e->getMessage()
        );

        echo $e->getMessage();

        return;
    }



    log_message(
        'error',
        'STRIPE EVENT: '.$event->type
    );


    $eventId = $event->id;



    if(
        $this->paymentService
             ->webhookExists($eventId)
    )
    {
        http_response_code(200);
        return;
    }



    $webhookId =
        $this->paymentService
             ->saveWebhookEvent(
                $event
             );



    try
    {

        switch($event->type)
{

    case 'checkout.session.completed':

        log_message(
            'error',
            'CHECKOUT SESSION COMPLETED RECEIVED'
        );

        $this->paymentService
             ->handleSuccessfulPayment(
                $event,
                $webhookId
             );

        break;


    case 'payment_intent.payment_failed':

        $this->paymentService
             ->handleFailedPayment(
                $event
             );

        break;


    default:

        log_message(
            'info',
            'Stripe ignored: '.$event->type
        );

}



        $this->paymentService
             ->markWebhookProcessed(
                $webhookId
             );



        http_response_code(200);

        echo json_encode([
            'status'=>'success'
        ]);

    }
    catch(\Throwable $e)
    {

        log_message(
            'error',
            'WEBHOOK ERROR: '.$e->getMessage()
        );


        log_message(
            'error',
            'FILE: '.$e->getFile()
            .' LINE: '.$e->getLine()
        );


        http_response_code(500);

    }
}


}