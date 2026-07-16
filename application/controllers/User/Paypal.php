<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Paypal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->service('PaypalService');
        $this->load->service('PaymentService');
        $this->load->service('AccountingService');

        $this->load->repository('OrderRepository');
        $this->load->repository('PaymentRepository');
        $this->load->repository('PaymentAttemptRepository');
        $this->load->repository('StripeTransactionRepository');
    }


    /**
     * PayPal return URL
     *
     * Example:
     * /payment/paypal/success?token=XXXX&PayerID=XXXX
     */
    public function success()
    {
        $paypalOrderId = $this->input->get('token');

        if (!$paypalOrderId) {
            show_error('Missing PayPal order ID.');
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Capture PayPal Payment
            |--------------------------------------------------------------------------
            */

            $result = $this->paypalservice->captureOrder($paypalOrderId);


            if (!$result['success']) {

                throw new Exception(
                    $result['message']
                );

            }


            $captureData = $result['data'];


            /*
            |--------------------------------------------------------------------------
            | Extract PayPal Capture Information
            |--------------------------------------------------------------------------
            */

            $purchaseUnit = 
                $captureData['purchase_units'][0];


            $capture =
                $purchaseUnit['payments']['captures'][0];


            $captureId = $capture['id'];

            $captureStatus = $capture['status'];

            $amount =
                $capture['amount']['value'];

            $currency =
                $capture['amount']['currency_code'];


            /*
            |--------------------------------------------------------------------------
            | Find Internal Order ID
            |--------------------------------------------------------------------------
            */

            $referenceId =
                $purchaseUnit['reference_id'];


            $internalOrderId =
                str_replace(
                    'order_',
                    '',
                    $referenceId
                );


            if (!$internalOrderId) {

                throw new Exception(
                    'Cannot find internal order ID.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Update Payment Data
            |--------------------------------------------------------------------------
            */

            $this->completePayment(
                $internalOrderId,
                $paypalOrderId,
                $captureId,
                $captureStatus,
                $amount,
                $currency,
                $captureData
            );


            $this->session->set_flashdata(
                'success',
                'Payment successful!'
            );


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT
            |--------------------------------------------------------------------------
            | Do NOT redirect back to this method.
            |--------------------------------------------------------------------------
            */

            redirect(
    'payment/success?token='.$paypalOrderId
);


        } catch(Exception $e) {


            log_message(
                'error',
                'PayPal Error: '.$e->getMessage()
            );


            $this->session->set_flashdata(
                'error',
                $e->getMessage()
            );


            redirect(
                'user/checkout/index'
            );

        }

    }




    /**
     * Cancel URL
     */
    public function cancel()
    {

        $this->session->set_flashdata(
            'error',
            'Payment cancelled.'
        );


        redirect(
            'user/checkout/index'
        );

    }




    /**
     * Complete Payment
     */
    private function completePayment(
        $orderId,
        $paypalOrderId,
        $captureId,
        $status,
        $amount,
        $currency,
        $payload
    )
    {


        /*
        |--------------------------------------------------------------------------
        | Find Payment
        |--------------------------------------------------------------------------
        */

        $payment =
            $this->paymentrepository
                 ->findByOrderId($orderId);


        if (!$payment) {

            throw new Exception(
                'Payment record not found.'
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Status IDs
        |--------------------------------------------------------------------------
        */

        $paidStatus =
            $this->db
                 ->get_where(
                    'lookups',
                    [
                        'group_id'=>3,
                        'code'=>'paid'
                    ]
                 )
                 ->row()
                 ->id;



        $attemptPaidStatus =
            $this->db
                 ->get_where(
                    'lookups',
                    [
                        'group_id'=>4,
                        'code'=>'paid'
                    ]
                 )
                 ->row()
                 ->id;



        /*
        |--------------------------------------------------------------------------
        | Update payments table
        |--------------------------------------------------------------------------
        */

        $this->paymentrepository->update(
            $payment->id,
            [
                'payment_method' => 'paypal',
                'status_lookup_id'=>$paidStatus,

                'paid_at'=>date(
                    'Y-m-d H:i:s'
                ),

                'updated_at'=>date(
                    'Y-m-d H:i:s'
                )
            ]
        );




        /*
        |--------------------------------------------------------------------------
        | Update payment_attempts table
        |--------------------------------------------------------------------------
        */


        $attempt =
            $this->paymentattemptrepository
                 ->findByPaymentId(
                    $payment->id
                 );


        if ($attempt) {


            $this->paymentattemptrepository->update(
                $attempt->id,
                [
                    'status_lookup_id'=>$attemptPaidStatus,

                    'updated_at'=>date(
                        'Y-m-d H:i:s'
                    )
                ]
            );


        }




        /*
        |--------------------------------------------------------------------------
        | Save PayPal transaction
        |
        | Using stripe_transactions temporarily
        |--------------------------------------------------------------------------
        */


        $this->stripetransactionrepository->create(
            [

                'payment_id'=>$payment->id,

                'payment_attempt_id'=>
                    $attempt ? $attempt->id : null,


                'provider'=>'paypal',


                // PayPal Order ID
                'stripe_session_id'=>
                    $paypalOrderId,


                // PayPal Capture ID
                'charge_id'=>
                    $captureId,


                'currency'=>$currency,


                'amount'=>$amount,


                'provider_status'=>$status,


                'raw_payload'=>
                    json_encode($payload),


                'created_at'=>date(
                    'Y-m-d H:i:s'
                ),


                'updated_at'=>date(
                    'Y-m-d H:i:s'
                )

            ]
        );




        /*
        |--------------------------------------------------------------------------
        | Settle invoice and generate receipt
        |--------------------------------------------------------------------------
        */

        $this->accountingservice->fulfillInvoiceAndReceipt(
            $orderId,
            (float) $amount
        );


        /*
        |--------------------------------------------------------------------------
        | Update Order Status
        |--------------------------------------------------------------------------
        */


        $this->orderrepository->update(
            $orderId,
            [
                'status_lookup_id'=>$paidStatus,

                'updated_at'=>date(
                    'Y-m-d H:i:s'
                )
            ]
        );

    }

}