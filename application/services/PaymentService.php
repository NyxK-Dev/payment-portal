<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class PaymentService
{


    protected $CI;



    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->repository(
        'PaymentRepository'
    );


    $this->CI->load->repository(
        'PaymentAttemptRepository'
    );

        

    }




    public function createPayment(
        array $order
    )
    {


        $paymentNo =
            'PAY-'
            .date('YmdHis');



        $paymentId =
            $this->CI
            ->paymentrepository
            ->create([


                'order_id'=>$order['id'],


                'payment_no'=>$paymentNo,


                'amount'=>$order['total'],


                'currency'=>'USD',


                'status_lookup_id'=>1,


                'version'=>1,


                'created_at'=>date(
                    'Y-m-d H:i:s'
                )


            ]);





        $attemptId =
            $this->CI
            ->paymentattemptrepository
            ->create([


                'payment_id'=>$paymentId,


                'attempt_no'=>1,


                'provider'=>'stripe',


                'amount'=>$order['total'],


                'status_lookup_id'=>1,


                'created_at'=>date(
                    'Y-m-d H:i:s'
                )

            ]);




        return [

            'id'=>$paymentId,

            'payment_no'=>$paymentNo,

            'attempt_id'=>$attemptId

        ];


    }




    public function saveStripeSession(
        $attemptId,
        $sessionId
    )
    {


        return $this->CI
        ->paymentattemptrepository
        ->update(

            $attemptId,

            [

            'stripe_session_id'=>$sessionId,


            'updated_at'=>date(
                'Y-m-d H:i:s'
            )

            ]

        );

    }


}