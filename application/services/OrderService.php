<?php

defined('BASEPATH') or exit('No direct script access allowed');


class OrderService
{

    protected $CI;


    public function __construct()
    {

        $this->CI = &get_instance();


        $this->CI->load->repository(
            'OrderRepository'
        );


        $this->CI->load->repository(
            'OrderItemRepository'
        );
    }




    public function getAllOrders()
    {
        return $this->CI
            ->orderrepository
            ->getAll();
    }

    public function createOrder(
        $userId,
        array $cart
    ) {


        $total = 0;


        foreach ($cart as $item) {

            $total +=
                $item['price']
                *
                $item['quantity'];
        }



        $orderNo =
            'ORD-'
            . date('YmdHis');



        $orderId =
            $this->CI
            ->orderrepository
            ->create([

                'user_id' => $userId,

                'order_no' => $orderNo,

                'status_lookup_id' => 5,

                'total_amount' => $total,

                'version' => 1,

                'created_at' => date(
                    'Y-m-d H:i:s'
                )

            ]);





        $items = [];


        foreach ($cart as $item) {

            $items[] = [

                'order_id' => $orderId,

                'product_id' => $item['product_id'],

                'quantity' => $item['quantity'],

                'unit_price' => $item['price'],

                'subtotal' =>
                $item['price']
                    *
                    $item['quantity'],

                'created_at' => date(
                    'Y-m-d H:i:s'
                )

            ];
        }



        $this->CI
            ->orderitemrepository
            ->createBatch(
                $items
            );




        return [

            'id' => $orderId,

            'order_no' => $orderNo,

            'total' => $total

        ];
    }


    public function getOrderHistory($userId, $filters = [])
    {


        $orders = $this->CI
            ->orderrepository
            ->getByUser(
                $userId,
                $filters
            );


        foreach ($orders as $order) {

            $order->items =
                $this->CI
                ->orderitemrepository
                ->getByOrderId($order->id);
        }


        return $orders;
    }

    public function getOrderDetail($id)
    {
        $order = $this->CI
            ->orderrepository
            ->findWithItems($id);

        if ($order) {

            $order->items = $this->CI
                ->orderitemrepository
                ->getByOrderId($id);
        }

        return $order;
    }
    public function updateStatus(
        $id,
        $statusId
    ) {
        return $this->CI
            ->orderrepository
            ->update(
                $id,
                [
                    'status_lookup_id' => $statusId
                ]
            );
    }
}
