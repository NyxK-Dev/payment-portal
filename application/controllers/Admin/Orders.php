<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->service('OrderService');
    }

    public function index()
    {
        $orders = $this->orderservice->getAllOrders();

        $data = [
            'title'  => 'Orders',
            'orders' => $orders
        ];

        $this->render('admin/orders/index', $data);
    }

    public function view($id)
    {
        $order = $this->orderservice->getOrderDetail($id);

        $data = [
            'title' => 'Order Detail',
            'order' => $order
        ];

        $this->render('admin/orders/detail', $data);
    }
    public function updateStatus($id)
    {
        $status = $this->input->post('status_lookup_id');

        $this->orderservice->updateStatus(
            $id,
            $status
        );

        redirect('admin/orders/view/' . $id);
    }
}
