<?php

use PHPUnit\Framework\TestCase;

class FakeInput
{
    public function post($key)
    {
        return null;
    }
}



class CheckoutServiceTest extends TestCase
{

    protected $service;

    protected $orderService;
    protected $paymentService;
    protected $stripeService;
    protected $idempotencyService;
    protected $idempotencyRepository;



    protected function setUp(): void
    {

        global $CI;


        $CI = new stdClass();



        /*
        |--------------------------------------------------------------------------
        | Mock Input
        |--------------------------------------------------------------------------
        */

        $CI->input =
            $this->getMockBuilder(FakeInput::class)
            ->onlyMethods([
                'post'
            ])
            ->getMock();


        $CI->input
            ->expects($this->any())
            ->method('post')
            ->with('idempotency_key')
            ->willReturn(
                'test-key'
            );




        /*
        |--------------------------------------------------------------------------
        | Mock Database Transaction
        |--------------------------------------------------------------------------
        */

        $CI->db =
            $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'trans_begin',
                'trans_commit',
                'trans_rollback'
            ])
            ->getMock();


        $CI->db
            ->method('trans_begin');


        $CI->db
            ->method('trans_commit');


        $CI->db
            ->method('trans_rollback');





        /*
        |--------------------------------------------------------------------------
        | Mock Services
        |--------------------------------------------------------------------------
        */

        $this->orderService =
            $this->createMock(
                OrderService::class
            );


        $this->paymentService =
            $this->createMock(
                PaymentService::class
            );


        $this->stripeService =
            $this->createMock(
                StripeService::class
            );


        $this->idempotencyService =
            $this->createMock(
                IdempotencyService::class
            );


        $this->idempotencyRepository =
            $this->createMock(
                IdempotencyInterface::class
            );





        $this->service =
            new CheckoutService(

                $this->orderService,

                $this->paymentService,

                $this->stripeService,

                $this->idempotencyService,

                $this->idempotencyRepository

            );
    }







    public function test_checkout_returns_cached_response()
    {


        $cachedResponse = [

            'success' => true,

            'url' => 'cached-url'

        ];



        $this->idempotencyService
            ->expects($this->once())
            ->method('start')
            ->with(
                'test-key',
                1,
                [
                    'product_id' => 10
                ]
            )
            ->willReturn([

                'duplicate' => true,

                'response' => $cachedResponse

            ]);




        $result =
            $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );



        $this->assertEquals(
            $cachedResponse,
            $result
        );
    }









    public function test_checkout_success()
    {


        $order = [

            'id' => 100

        ];


        $payment = [

            'attempt_id' => 50

        ];




        $this->idempotencyService
            ->method('start')
            ->willReturn([

                'duplicate' => false

            ]);





        $this->orderService
            ->expects($this->once())
            ->method('createOrder')
            ->with(
                1,
                [
                    'product_id' => 10
                ]
            )
            ->willReturn(
                $order
            );





        $this->paymentService
            ->expects($this->once())
            ->method('createPayment')
            ->with(
                $order
            )
            ->willReturn(
                $payment
            );





        $this->stripeService
            ->expects($this->once())
            ->method('createCheckoutSession')
            ->with(
                $order,
                $payment,
                [
                    'product_id' => 10
                ],
                'test-key'
            )
            ->willReturn([

                'success' => true,

                'url' => 'stripe-url',

                'session_id' => 'session123'

            ]);





        $this->paymentService
            ->expects($this->once())
            ->method('saveStripeSession')
            ->with(
                50,
                'session123'
            );





        $this->idempotencyRepository
            ->expects($this->once())
            ->method('complete')
            ->with(
                'test-key',
                [
                    'success' => true,
                    'url' => 'stripe-url',
                    'order_id' => 100
                ],
                200
            );





        $result =
            $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );




        $this->assertTrue(
            $result['success']
        );


        $this->assertEquals(
            'stripe-url',
            $result['url']
        );


        $this->assertEquals(
            100,
            $result['order_id']
        );
    }

    public function test_checkout_fails_when_stripe_failed()
    {


        $this->idempotencyService
            ->method('start')
            ->willReturn([

                'duplicate' => false

            ]);




        $this->orderService
            ->method('createOrder')
            ->willReturn([

                'id' => 100

            ]);




        $this->paymentService
            ->method('createPayment')
            ->willReturn([

                'attempt_id' => 50

            ]);




        $this->stripeService
            ->method('createCheckoutSession')
            ->willReturn([

                'success' => false,

                'message' => 'Stripe failed'

            ]);




        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail')
            ->with(
                'test-key',
                'Stripe failed'
            );




        $this->expectException(
            Exception::class
        );




        $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );
    }

    public function test_checkout_without_idempotency_key()
    {
        global $CI;

        $input = $this->getMockBuilder(FakeInput::class)
            ->onlyMethods(['post'])
            ->getMock();

        $input->method('post')
            ->with('idempotency_key')
            ->willReturn(null);

        $CI->input = $input;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing Idempotency-Key');

        $this->service->checkout(
            1,
            ['product_id' => 10]
        );
    }
    public function test_checkout_fails_when_create_order_throws_exception()
    {
        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);

        $this->orderService
            ->method('createOrder')
            ->willThrowException(
                new Exception('Order failed')
            );

        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail')
            ->with(
                'test-key',
                'Order failed'
            );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order failed');

        $this->service->checkout(
            1,
            ['product_id' => 10]
        );
    }
    public function test_checkout_fails_when_create_payment_throws_exception()
    {
        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);

        $this->orderService
            ->method('createOrder')
            ->willReturn([
                'id' => 100
            ]);

        $this->paymentService
            ->method('createPayment')
            ->willThrowException(
                new Exception('Payment failed')
            );

        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail')
            ->with(
                'test-key',
                'Payment failed'
            );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Payment failed');

        $this->service->checkout(
            1,
            ['product_id' => 10]
        );
    }
    public function test_checkout_fails_when_save_stripe_session_throws_exception()
    {
        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);

        $this->orderService
            ->method('createOrder')
            ->willReturn([
                'id' => 100
            ]);

        $this->paymentService
            ->method('createPayment')
            ->willReturn([
                'attempt_id' => 50
            ]);

        $this->stripeService
            ->method('createCheckoutSession')
            ->willReturn([
                'success' => true,
                'url' => 'stripe-url',
                'session_id' => 'abc'
            ]);

        $this->paymentService
            ->method('saveStripeSession')
            ->willThrowException(
                new Exception('Save failed')
            );

        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail')
            ->with(
                'test-key',
                'Save failed'
            );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Save failed');

        $this->service->checkout(
            1,
            ['product_id' => 10]
        );
    }
    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_checkout_success_commits_transaction()
    {

        global $CI;


        $CI->db
            ->expects($this->once())
            ->method('trans_commit');



        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);



        $this->orderService
            ->method('createOrder')
            ->willReturn([
                'id' => 100
            ]);



        $this->paymentService
            ->method('createPayment')
            ->willReturn([
                'attempt_id' => 50
            ]);



        $this->stripeService
            ->method('createCheckoutSession')
            ->willReturn([
                'success' => true,
                'url' => 'url',
                'session_id' => 'session'
            ]);



        $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );
    }






    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    public function test_checkout_rollbacks_when_order_failed()
    {

        global $CI;


        $CI->db
            ->expects($this->once())
            ->method('trans_rollback');



        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);



        $this->orderService
            ->method('createOrder')
            ->willThrowException(
                new Exception('Order Error')
            );



        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail')
            ->with(
                'test-key',
                'Order Error'
            );



        $this->expectException(
            Exception::class
        );



        $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );
    }







    public function test_checkout_stripe_exception()
    {

        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);



        $this->orderService
            ->method('createOrder')
            ->willReturn([
                'id' => 1
            ]);



        $this->paymentService
            ->method('createPayment')
            ->willReturn([
                'attempt_id' => 10
            ]);



        $this->stripeService
            ->method('createCheckoutSession')
            ->willThrowException(
                new Exception('Stripe Error')
            );



        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail')
            ->with(
                'test-key',
                'Stripe Error'
            );



        $this->expectException(
            Exception::class
        );



        $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );
    }







    /*
    |--------------------------------------------------------------------------
    | IDEMPOTENCY CASES
    |--------------------------------------------------------------------------
    */


    public function test_duplicate_checkout_does_not_create_order()
    {


        $this->idempotencyService
            ->expects($this->once())
            ->method('start')
            ->willReturn([

                'duplicate' => true,

                'response' => [
                    'success' => true
                ]

            ]);



        $this->orderService
            ->expects($this->never())
            ->method('createOrder');



        $result =
            $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );



        $this->assertTrue(
            $result['success']
        );
    }








    public function test_failed_checkout_does_not_complete_idempotency()
    {

        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);



        $this->orderService
            ->method('createOrder')
            ->willThrowException(
                new Exception('failed')
            );



        $this->idempotencyRepository
            ->expects($this->never())
            ->method('complete');



        $this->expectException(
            Exception::class
        );



        $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );
    }








    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    public function test_checkout_empty_cart()
    {

        $this->expectException(
            Exception::class
        );



        $this->expectExceptionMessage(
            'Cart is required'
        );



        $this->service
            ->checkout(
                1,
                []
            );
    }








    public function test_checkout_invalid_user()
    {

        $this->expectException(
            Exception::class
        );



        $this->service
            ->checkout(
                0,
                [
                    'product_id' => 10
                ]
            );
    }








    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    public function test_checkout_multiple_products()
    {


        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);



        $this->orderService
            ->method('createOrder')
            ->willReturn([
                'id' => 100
            ]);



        $this->paymentService
            ->method('createPayment')
            ->willReturn([
                'attempt_id' => 1
            ]);



        $this->stripeService
            ->method('createCheckoutSession')
            ->with(
                [
                    'id' => 100
                ],
                [
                    'attempt_id' => 1
                ],
                [
                    [
                        'product_id' => 10
                    ],
                    [
                        'product_id' => 20
                    ]
                ],
                'test-key'
            )
            ->willReturn([
                'success' => true,
                'url' => 'url',
                'session_id' => 'abc'
            ]);



        $result =
            $this->service
            ->checkout(
                1,
                [
                    [
                        'product_id' => 10
                    ],
                    [
                        'product_id' => 20
                    ]
                ]
            );



        $this->assertTrue(
            $result['success']
        );
    }







    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */


    public function test_complete_called_only_after_payment_success()
    {

        $this->idempotencyService
            ->method('start')
            ->willReturn([
                'duplicate' => false
            ]);



        $this->orderService
            ->method('createOrder')
            ->willReturn([
                'id' => 10
            ]);



        $this->paymentService
            ->method('createPayment')
            ->willReturn([
                'attempt_id' => 5
            ]);



        $this->stripeService
            ->method('createCheckoutSession')
            ->willReturn([
                'success' => true,
                'url' => 'url',
                'session_id' => 'abc'
            ]);



        $this->idempotencyRepository
            ->expects($this->once())
            ->method('complete');



        $this->service
            ->checkout(
                1,
                [
                    'product_id' => 10
                ]
            );
    }
}
