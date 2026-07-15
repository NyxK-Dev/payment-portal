<?php

defined('BASEPATH') or exit('No direct script access allowed');


class OrderService
{

    protected $orderRepository;

    protected $orderItemRepository;



    public function __construct(
        OrderInterface $orderRepository,
        OrderItemInterface $orderItemRepository
    ) {

        $this->orderRepository = $orderRepository;

        $this->orderItemRepository = $orderItemRepository;

    }




    public function getAllOrders()
    {
        return $this->orderRepository
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
            $this->orderRepository
                ->create([

                    'user_id'=>$userId,

                    'order_no'=>$orderNo,

                    'status_lookup_id'=>5,

                    'total_amount'=>$total,

                    'version'=>1,

                    'created_at'=>date(
                        'Y-m-d H:i:s'
                    )

                ]);






        $items = [];



        foreach ($cart as $item) {

            $items[]=[

                'order_id'=>$orderId,

                'product_id'=>$item['product_id'],

                'quantity'=>$item['quantity'],

                'unit_price'=>$item['price'],

                'subtotal'=>
                    $item['price']
                    *
                    $item['quantity'],

                'created_at'=>date(
                    'Y-m-d H:i:s'
                )

            ];

        }




        $this->orderItemRepository
            ->createBatch(
                $items
            );





        return [

            'id'=>$orderId,

            'order_no'=>$orderNo,

            'total'=>$total

        ];

    }







    public function getOrderHistory(
        $userId,
        $filters=[]
    ) {


        $orders =
            $this->orderRepository
                ->getByUser(
                    $userId,
                    $filters
                );



        foreach ($orders as $order) {


            $order->items =
                $this->orderItemRepository
                    ->getByOrderId(
                        $order->id
                    );

        }



        return $orders;

    }








    public function getOrderDetail($id)
    {

        $order =
            $this->orderRepository
                ->findWithItems($id);



        if ($order) {


            $order->items =
                $this->orderItemRepository
                    ->getByOrderId(
                        $id
                    );

        }



        return $order;

    }







    public function updateStatus(
        $id,
        $statusId
    ) {

        return $this->orderRepository
            ->update(
                $id,
                [
                    'status_lookup_id'=>$statusId
                ]
            );

    }


}