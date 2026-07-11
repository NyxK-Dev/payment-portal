<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->service('PaymentService');
    }

    /**
     * Stripe success URL
     */
    public function success()
    {
        $sessionId = $this->input->get('session_id');

        if (empty($sessionId)) {
            redirect('user/cart/index');
        }

        try {
            // Process post-payment records (Invoice, Receipt, Status Updates)
            $this->paymentservice->fulfillPaymentBySession($sessionId);

            // Clear the cart session since they bought the items successfully
            $this->session->unset_userdata('cart');

            $this->render(
                'user/checkout/success',
                [
                    'title'      => 'Payment Successful',
                    'session_id' => $sessionId
                ]
            );
        } catch (Exception $e) {
            log_message('error', 'Fulfillment failed for Stripe Session ' . $sessionId . ': ' . $e->getMessage());

            $this->session->set_flashdata('error', 'Payment confirmed, but system fulfillment failed. Please contact support.');
            redirect('user/cart/index');
        }
    }

    /**
     * Stripe cancel URL
     */
    public function cancel()
    {
        $this->render(
            'user/checkout/cancel',
            [
                'title' => 'Payment Cancelled'
            ]
        );
    }
}
