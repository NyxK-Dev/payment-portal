<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class OrderService
{

    protected $CI;


    public function __construct()
    {

        $this->CI =& get_instance();


        $this->CI->load->repository(
        'OrderRepository'
    );


    $this->CI->load->repository(
        'OrderItemRepository'
    );

    }




    public function createOrder(
        $userId,
        array $cart
    )
    {


        $total = 0;


        foreach($cart as $item)
        {

            $total +=
                $item['price']
                *
                $item['quantity'];

        }



        $orderNo =
            'ORD-'
            .date('YmdHis');



        $orderId =
            $this->CI
            ->orderrepository
            ->create([

                'user_id'=>$userId,

                'order_no'=>$orderNo,

                'status_lookup_id'=>1,

                'total_amount'=>$total,

                'version'=>1,

                'created_at'=>date(
                    'Y-m-d H:i:s'
                )

            ]);





        $items=[];


        foreach($cart as $item)
        {

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



        $this->CI
        ->orderitemrepository
        ->createBatch(
            $items
        );




        return [

            'id'=>$orderId,

            'order_no'=>$orderNo,

            'total'=>$total

        ];

    }


}