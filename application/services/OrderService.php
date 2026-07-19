<?php

defined('BASEPATH') OR exit('No direct script access allowed');


require_once APPPATH . 'interfaces/OrderInterface.php';
require_once APPPATH . 'interfaces/OrderItemInterface.php';

class OrderService
{

    protected $orderRepository;
    protected $orderItemRepository;


    public function __construct(
        OrderInterface $orderRepository,
        OrderItemInterface $orderItemRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }





    /*
    |--------------------------------------------------------------------------
    | Get All Orders
    |--------------------------------------------------------------------------
    */

    public function getAllOrders()
    {
        return $this->orderRepository
            ->getAll();
    }







    /*
    |--------------------------------------------------------------------------
    | Create Order
    |--------------------------------------------------------------------------
    */

    public function createOrder(
        $userId,
        array $cart
    ) {


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */


        if(
            !is_numeric($userId)
            ||
            $userId <= 0
        ){

            throw new InvalidArgumentException(
                "Invalid user id"
            );

        }





        $total = 0;
        foreach ($cart as $item) {


            if(
                !isset($item['price'])
                ||
                !isset($item['quantity'])
            ){

                throw new InvalidArgumentException(
                    "Invalid cart item"
                );

            }



            $total +=
                $item['price']
                *
                $item['quantity'];

        }

        // 3. Generate Order Number
//         $orderNo = 'ORD-' . date('YmdHis');

//         // 4. Create Order
//         $orderId = $this->CI->orderrepository->create([
//             'user_id'          => $userId,
//             'order_no'         => $orderNo,
//             'status_lookup_id' => 5, // Pending Status
//             'total_amount'     => $total,
//             'version'          => 1,
//             'created_at'       => date('Y-m-d H:i:s')
//         ]);





        $orderNo =
            'ORD-'
            .
            date('YmdHis');








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






        $this->orderItemRepository
            ->createBatch(
                $items
            );






        return [
            'id'       => $orderId,
            'order_no' => $orderNo,
            'total'    => $total
        ];

    }

    /**
     * Validate Order Items structure and payload data.
     */
    protected function validateItems(array $cart)
    {
        if (empty($cart)) {
            return false;
        }

        foreach ($cart as $item) {
            // Check keys exist
            if (!isset($item['product_id']) || !isset($item['price']) || !isset($item['quantity'])) {
                return false;
            }

            // Check types are numeric
            if (!is_numeric($item['product_id']) || !is_numeric($item['price']) || !is_numeric($item['quantity'])) {
                return false;
            }

            // Check quantity boundaries
            if ($item['quantity'] <= 0) {
                return false;
            }
        }

        return true;
    }





    /*
    |--------------------------------------------------------------------------
    | Order History
    |--------------------------------------------------------------------------
    */

    public function getOrderHistory(
        $userId,
        $filters=[]
    ) {


        if(
            !is_numeric($userId)
            ||
            $userId <=0
        ){

            throw new InvalidArgumentException(
                "Invalid user id"
            );

        }



        $orders =
            $this->orderRepository
                ->getByUser(
                    $userId,
                    $filters
                );






        foreach($orders as $order)
        {


            $order->items =
                $this->orderItemRepository
                    ->getByOrderId(
                        $order->id
                    );


        }




        return $orders;

    }









    /*
    |--------------------------------------------------------------------------
    | Order Detail
    |--------------------------------------------------------------------------
    */

    public function getOrderDetail(
        $id
    ) {


        if(
            !is_numeric($id)
            ||
            $id <=0
        ){

            throw new InvalidArgumentException(
                "Invalid order id"
            );

        }




        $order =
            $this->orderRepository
                ->findWithItems(
                    $id
                );




        if($order)
        {

            $order->items =
                $this->orderItemRepository
                    ->getByOrderId(
                        $id
                    );

        }





        return $order;

    }









    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        $id,
        $statusId
    ) {



        if(
            !is_numeric($id)
            ||
            $id <=0
        ){

            throw new InvalidArgumentException(
                "Invalid order id"
            );

        }




        if(
            !is_numeric($statusId)
            ||
            $statusId <=0
        ){

            throw new InvalidArgumentException(
                "Invalid status id"
            );

        }






        return $this->orderRepository
            ->update(

                $id,

                [

                    'status_lookup_id'=>$statusId

                ]

            );

    }


}