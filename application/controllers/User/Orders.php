<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->service('OrderService');
    }


    public function history()
    {

        $userId = $this->session->userdata('user_id');


        $filters = [

            'keyword' => $this->input->get('keyword'),

            'from' => $this->input->get('from'),

            'to' => $this->input->get('to'),

        ];



        $orders = $this->orderservice
            ->getOrderHistory(
                $userId,
                $filters
            );



        $data = [

            'title' => 'Order History',

            'orders' => $orders

        ];



        $this->render(
            'user/orders/history',
            $data
        );
    }
}