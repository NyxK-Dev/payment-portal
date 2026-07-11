<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Payment extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();
    }





    /**
     * Stripe success URL
     */
    // public function success()
    // {


    //     $sessionId =
    //         $this->input
    //              ->get('session_id');



    //     $this->render(
    //         'user/checkout/success',
    //         [
    //             'title'=>'Payment Successful',
    //             'session_id'=>$sessionId
    //         ]
    //     );

    // }
    public function success()
{
    // Clear the cart item from session now that they are checking out successfully
    $this->session->unset_userdata('cart');

    // Render your beautiful success page
    $this->render('user/checkout/success', [
        'title' => 'Thank you for your order!'
    ]);
}







    /**
     * Stripe cancel URL
     */
    public function cancel()
    {


        $this->render(
            'user/checkout/cancel',
            [
                'title'=>'Payment Cancelled'
            ]
        );

    }



}