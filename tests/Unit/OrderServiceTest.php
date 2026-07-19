<?php

use PHPUnit\Framework\TestCase;


class OrderServiceTest extends TestCase
{

    protected $service;

    protected $orderRepository;

    protected $orderItemRepository;



    protected function setUp(): void
    {

        $this->orderRepository =
            $this->createMock(
                OrderInterface::class
            );


        $this->orderItemRepository =
            $this->createMock(
                OrderItemInterface::class
            );


        $this->service =
            new OrderService(
                $this->orderRepository,
                $this->orderItemRepository
            );
    }



    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_create_order_success()
    {

        $cart = [

            [
                'product_id'=>10,
                'price'=>100,
                'quantity'=>2
            ],

            [
                'product_id'=>20,
                'price'=>50,
                'quantity'=>1
            ]

        ];



        $this->orderRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(100);



        $this->orderItemRepository
            ->expects($this->once())
            ->method('createBatch')
            ->with(
                $this->callback(function($items){

                    return count($items)==2
                    &&
                    $items[0]['order_id']==100
                    &&
                    $items[0]['subtotal']==200;

                })
            );



        $result =
            $this->service
            ->createOrder(
                1,
                $cart
            );



        $this->assertEquals(
            100,
            $result['id']
        );


        $this->assertEquals(
            250,
            $result['total']
        );


        $this->assertStringStartsWith(
            'ORD-',
            $result['order_no']
        );

    }





    public function test_get_all_orders_success()
    {

        $orders = [

            (object)[
                'id'=>1
            ]

        ];



        $this->orderRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($orders);



        $result =
            $this->service
            ->getAllOrders();



        $this->assertEquals(
            $orders,
            $result
        );

    }





    public function test_get_order_detail_success()
    {

        $order =
            (object)[
                'id'=>1
            ];



        $items=[
            (object)[
                'product_id'=>10
            ]
        ];



        $this->orderRepository
            ->expects($this->once())
            ->method('findWithItems')
            ->with(1)
            ->willReturn($order);



        $this->orderItemRepository
            ->expects($this->once())
            ->method('getByOrderId')
            ->with(1)
            ->willReturn($items);



        $result =
            $this->service
            ->getOrderDetail(1);



        $this->assertEquals(
            $items,
            $result->items
        );

    }






    public function test_update_status_success()
    {

        $this->orderRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                [
                    'status_lookup_id'=>8
                ]
            )
            ->willReturn(true);



        $this->assertTrue(
            $this->service
            ->updateStatus(
                1,
                8
            )
        );

    }







    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_order_repository_failed()
{

    $this->orderRepository
        ->expects($this->once())
        ->method('create')
        ->willReturn(false);


    $result =
        $this->service
        ->createOrder(
            1,
            []
        );


    $this->assertFalse(
        $result['id']
    );

}






    public function test_update_status_failed()
    {

        $this->orderRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);



        $result =
            $this->service
            ->updateStatus(
                1,
                8
            );



        $this->assertFalse(
            $result
        );

    }






    public function test_get_order_detail_not_found()
    {

        $this->orderRepository
            ->expects($this->once())
            ->method('findWithItems')
            ->willReturn(null);



        $result =
            $this->service
            ->getOrderDetail(
                999
            );



        $this->assertNull(
            $result
        );

    }








    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_order_invalid_user()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service
            ->createOrder(
                0,
                []
            );

    }





    public function test_create_order_invalid_cart()
    {

        $this->expectException(
            TypeError::class
        );


        $this->service
            ->createOrder(
                1,
                null
            );

    }





    public function test_update_status_invalid_status()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service
            ->updateStatus(
                1,
                0
            );

    }







    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_order_empty_cart()
    {

        $this->orderRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $this->orderItemRepository
            ->expects($this->once())
            ->method('createBatch')
            ->with([]);



        $result =
            $this->service
            ->createOrder(
                1,
                []
            );



        $this->assertEquals(
            0,
            $result['total']
        );

    }





    public function test_large_quantity()
    {

        $cart=[

            [
                'product_id'=>1,
                'price'=>100,
                'quantity'=>100000
            ]

        ];



        $this->orderRepository
            ->method('create')
            ->willReturn(1);



        $this->orderItemRepository
            ->method('createBatch');



        $result =
            $this->service
            ->createOrder(
                1,
                $cart
            );



        $this->assertEquals(
            10000000,
            $result['total']
        );

    }







    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */



    public function test_order_total_calculation()
    {

        $cart=[

            [
                'product_id'=>1,
                'price'=>20,
                'quantity'=>3
            ]

        ];



        $this->orderRepository
            ->method('create')
            ->willReturn(1);



        $result =
            $this->service
            ->createOrder(
                1,
                $cart
            );



        $this->assertEquals(
            60,
            $result['total']
        );

    }





    public function test_order_item_subtotal_calculation()
    {

        $cart=[

            [
                'product_id'=>1,
                'price'=>20,
                'quantity'=>3
            ]

        ];



        $this->orderRepository
            ->method('create')
            ->willReturn(5);



        $this->orderItemRepository
            ->expects($this->once())
            ->method('createBatch')
            ->with(
                $this->callback(function($items){

                    return
                    $items[0]['subtotal']==60;

                })
            );



        $this->service
            ->createOrder(
                1,
                $cart
            );

    }





    public function test_repository_create_called_once()
    {

        $this->orderRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $this->service
            ->createOrder(
                1,
                []
            );

    }

}