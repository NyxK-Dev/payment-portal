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



    public function test_create_order()
    {

        $cart = [

            [
                'product_id' => 10,
                'price' => 100,
                'quantity' => 2
            ],

            [
                'product_id' => 20,
                'price' => 50,
                'quantity' => 1
            ]

        ];



        $this->orderRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(
                    function ($data) {

                        return

                            $data['user_id'] === 1

                            &&

                            $data['status_lookup_id'] === 5

                            &&

                            $data['total_amount'] === 250;
                    }
                )
            )
            ->willReturn(100);



        $this->orderItemRepository
            ->expects($this->once())
            ->method('createBatch')
            ->with(
                $this->callback(
                    function ($items) {

                        return count($items) === 2

                            &&

                            $items[0]['order_id'] === 100

                            &&

                            $items[0]['product_id'] === 10

                            &&

                            $items[0]['subtotal'] === 200;
                    }
                )
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




    public function test_get_all_orders()
    {

        $orders = [

            (object)[
                'id' => 1
            ],

            (object)[
                'id' => 2
            ]

        ];



        $this->orderRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn(
                $orders
            );



        $result =
            $this->service
            ->getAllOrders();



        $this->assertCount(
            2,
            $result
        );


        $this->assertEquals(
            1,
            $result[0]->id
        );
    }




    public function test_update_status()
    {

        $this->orderRepository
            ->expects($this->once())
            ->method('update')
            ->with(

                100,

                [
                    'status_lookup_id' => 8
                ]

            )
            ->willReturn(true);



        $result =
            $this->service
            ->updateStatus(
                100,
                8
            );



        $this->assertTrue(
            $result
        );
    }
    public function test_get_order_history()
    {
        $orders = [
            (object)['id' => 100]
        ];

        $items = [
            (object)['product_id' => 10]
        ];

        $this->orderRepository
            ->expects($this->once())
            ->method('getByUser')
            ->with(1, [])
            ->willReturn($orders);

        $this->orderItemRepository
            ->expects($this->once())
            ->method('getByOrderId')
            ->with(100)
            ->willReturn($items);

        $result = $this->service->getOrderHistory(1);

        $this->assertCount(1, $result);
        $this->assertEquals($items, $result[0]->items);
    }

    public function test_get_order_detail()
    {
        $order = (object)[
            'id' => 100
        ];

        $items = [
            (object)['product_id' => 10]
        ];

        $this->orderRepository
            ->expects($this->once())
            ->method('findWithItems')
            ->with(100)
            ->willReturn($order);

        $this->orderItemRepository
            ->expects($this->once())
            ->method('getByOrderId')
            ->with(100)
            ->willReturn($items);

        $result = $this->service->getOrderDetail(100);

        $this->assertEquals($items, $result->items);
    }
    public function test_get_order_detail_not_found()
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('findWithItems')
            ->with(999)
            ->willReturn(null);

        $this->assertNull(
            $this->service->getOrderDetail(999)
        );
    }
    public function test_update_status_failed()
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->updateStatus(
                100,
                8
            )
        );
    }
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

        $result = $this->service->createOrder(
            1,
            []
        );

        $this->assertEquals(0, $result['total']);
    }
}
