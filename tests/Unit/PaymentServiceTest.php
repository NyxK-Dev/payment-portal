<?php

use PHPUnit\Framework\TestCase;


class PaymentServiceTest extends TestCase
{

    protected $service;

    protected $paymentRepository;
    protected $paymentAttemptRepository;
    protected $stripeWebhookEventRepository;
    protected $stripeTransactionRepository;
    protected $paymentEventRepository;
    protected $orderRepository;
    protected $orderItemRepository;
    protected $productRepository;
    protected $accountingService;



    protected function setUp(): void
    {

        global $CI;


        $CI = new stdClass();


        $CI->db =
            $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'trans_begin',
                'trans_commit',
                'trans_rollback',
                'get_where'
            ])
            ->getMock();



        $CI->db->method('trans_begin');
        $CI->db->method('trans_commit');
        $CI->db->method('trans_rollback');




        $this->paymentRepository =
            $this->createMock(
                PaymentInterface::class
            );


        $this->paymentAttemptRepository =
            $this->createMock(
                PaymentAttemptInterface::class
            );


        $this->stripeWebhookEventRepository =
            $this->createMock(
                StripeWebhookEventInterface::class
            );


        $this->stripeTransactionRepository =
            $this->createMock(
                StripeTransactionInterface::class
            );


        $this->paymentEventRepository =
            $this->createMock(
                PaymentEventInterface::class
            );


        $this->orderRepository =
            $this->createMock(
                OrderInterface::class
            );


        $this->orderItemRepository =
            $this->createMock(
                OrderItemInterface::class
            );


        $this->productRepository =
            $this->createMock(
                ProductInterface::class
            );


        $this->accountingService =
            $this->createMock(
                AccountingService::class
            );




        $this->service =
            new PaymentService(

                $this->paymentRepository,

                $this->paymentAttemptRepository,

                $this->stripeWebhookEventRepository,

                $this->stripeTransactionRepository,

                $this->paymentEventRepository,

                $this->orderRepository,

                $this->orderItemRepository,

                $this->productRepository,

                $this->accountingService

            );
    }



    /*
    |--------------------------------------------------------------------------
    | CREATE PAYMENT SUCCESS
    |--------------------------------------------------------------------------
    */


    public function test_create_payment_success()
    {


        $order = [

            'id' => 100,

            'total' => 50

        ];



        $this->paymentRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(10);



        $this->accountingService
            ->expects($this->once())
            ->method('createPendingInvoice')
            ->willReturn(20);



        $this->paymentAttemptRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(30);



        $result =
            $this->service
            ->createPayment(
                $order
            );



        $this->assertIsArray(
            $result
        );


        $this->assertEquals(
            10,
            $result['id']
        );


        $this->assertEquals(
            20,
            $result['invoice_id']
        );


        $this->assertEquals(
            30,
            $result['attempt_id']
        );


        $this->assertStringStartsWith(
            'PAY-',
            $result['payment_no']
        );
    }





    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */


    public function test_create_payment_invalid_order_id()
    {

        $this->expectException(Exception::class);


        $this->service
            ->createPayment([

                'id' => null,

                'total' => 100

            ]);
    }




    public function test_create_payment_invalid_amount()
    {

        $this->expectException(Exception::class);


        $this->service
            ->createPayment([

                'id' => 1,

                'total' => 0

            ]);
    }





    /*
    |--------------------------------------------------------------------------
    | PAYMENT REPOSITORY FAILURE
    |--------------------------------------------------------------------------
    */


    public function test_create_payment_repository_failed()
    {

        $this->paymentRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);



        $this->expectException(Exception::class);


        $this->expectExceptionMessage(
            'Payment creation failed'
        );



        $this->service
            ->createPayment([

                'id' => 1,

                'total' => 100

            ]);
    }






    /*
    |--------------------------------------------------------------------------
    | INVOICE FAILURE
    |--------------------------------------------------------------------------
    */


    public function test_create_payment_invoice_failed()
    {

        $order = [

            'id' => 1,

            'total' => 100

        ];



        $this->paymentRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(10);



        $this->accountingService
            ->expects($this->once())
            ->method('createPendingInvoice')
            ->with($order)
            ->willReturn(0);



        $this->expectException(Exception::class);


        $this->expectExceptionMessage(
            'Invoice creation failed'
        );



        $this->service
            ->createPayment(
                $order
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT ATTEMPT FAILURE
    |--------------------------------------------------------------------------
    */


    public function test_create_payment_attempt_failed()
    {

        $this->paymentRepository
            ->method('create')
            ->willReturn(10);



        $this->accountingService
            ->method('createPendingInvoice')
            ->willReturn(20);



        $this->paymentAttemptRepository
            ->method('create')
            ->willReturn(false);



        $this->expectException(Exception::class);


        $this->expectExceptionMessage(
            'Payment attempt creation failed'
        );



        $this->service
            ->createPayment([

                'id' => 1,

                'total' => 100

            ]);
    }





    /*
    |--------------------------------------------------------------------------
    | BUSINESS RULE
    |--------------------------------------------------------------------------
    */


    public function test_payment_amount_from_order()
    {


        $this->paymentRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['amount'] == 999;
                })
            )
            ->willReturn(1);



        $this->accountingService
            ->method('createPendingInvoice')
            ->willReturn(2);



        $this->paymentAttemptRepository
            ->method('create')
            ->willReturn(3);



        $this->service
            ->createPayment([

                'id' => 1,

                'total' => 999

            ]);
    }





    public function test_payment_number_format()
    {


        $this->paymentRepository
            ->method('create')
            ->willReturn(1);



        $this->accountingService
            ->method('createPendingInvoice')
            ->willReturn(2);



        $this->paymentAttemptRepository
            ->method('create')
            ->willReturn(3);



        $result =
            $this->service
            ->createPayment([

                'id' => 1,

                'total' => 50

            ]);



        $this->assertMatchesRegularExpression(

            '/^PAY-\d{17}$/',

            $result['payment_no']

        );
    }






    /*
    |--------------------------------------------------------------------------
    | STRIPE SESSION
    |--------------------------------------------------------------------------
    */


    public function test_save_stripe_session_success()
    {


        $this->paymentAttemptRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->saveStripeSession(
                    1,
                    'sess_123'
                )

        );
    }




    public function test_save_stripe_session_failed()
    {


        $this->paymentAttemptRepository
            ->method('update')
            ->willReturn(false);



        $this->assertFalse(

            $this->service
                ->saveStripeSession(
                    1,
                    'sess'
                )

        );
    }







    /*
    |--------------------------------------------------------------------------
    | WEBHOOK
    |--------------------------------------------------------------------------
    */


    public function test_webhook_exists()
    {

        $this->stripeWebhookEventRepository
            ->method('existsByEventId')
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->webhookExists(
                    'evt_1'
                )

        );
    }




    public function test_webhook_not_exists()
    {

        $this->stripeWebhookEventRepository
            ->method('existsByEventId')
            ->willReturn(false);



        $this->assertFalse(

            $this->service
                ->webhookExists(
                    'evt_x'
                )

        );
    }





    public function test_save_webhook_event_success()
    {

        $event = (object)[

            'id' => 'evt_1',

            'type' => 'payment.success'

        ];



        $this->stripeWebhookEventRepository
            ->method('create')
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->saveWebhookEvent(
                    $event
                )

        );
    }




    public function test_save_webhook_event_failed()
    {

        $event = (object)[

            'id' => 'evt',

            'type' => 'payment'

        ];



        $this->stripeWebhookEventRepository
            ->method('create')
            ->willReturn(false);



        $this->assertFalse(

            $this->service
                ->saveWebhookEvent(
                    $event
                )

        );
    }





    /*
    |--------------------------------------------------------------------------
    | FAILED PAYMENT
    |--------------------------------------------------------------------------
    */


    public function test_failed_payment_without_payment_id()
    {


        $event = (object)[

            'data' => (object)[

                'object' => new stdClass()

            ]

        ];



        $this->paymentRepository
            ->expects($this->never())
            ->method('update');



        $this->service
            ->handleFailedPayment(
                $event
            );


        $this->assertTrue(true);
    }






    /*
    |--------------------------------------------------------------------------
    | WEBHOOK PROCESSED
    |--------------------------------------------------------------------------
    */


    public function test_mark_webhook_processed()
    {


        $this->stripeWebhookEventRepository
            ->method('update')
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->markWebhookProcessed(
                    1
                )

        );
    }




    /*
    |--------------------------------------------------------------------------
    | EDGE CASE
    |--------------------------------------------------------------------------
    */


    public function test_fulfill_payment_attempt_missing()
    {

        $this->paymentAttemptRepository
            ->method('findBySessionId')
            ->willReturn(null);



        $this->expectException(Exception::class);



        $this->service
            ->fulfillPaymentBySession(
                'wrong'
            );
    }
    
}
