<?php

use PHPUnit\Framework\TestCase;


class CheckoutServiceTest extends TestCase
{

    private $gatewayResolver;
    private $orderService;
    private $paymentService;
    private $idempotencyService;
    private $idempotencyRepository;

    private $service;



    protected function setUp(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Fake CodeIgniter Instance
        |--------------------------------------------------------------------------
        */

        $CI = new stdClass();


        $CI->input = new class {

            public function post($key)
            {
                return 'test-key-123';
            }

        };



        $CI->db = new class {

            public function trans_begin()
            {

            }


            public function trans_commit()
            {

            }


            public function trans_rollback()
            {

            }

        };


        $GLOBALS['CI'] = $CI;



        /*
        |--------------------------------------------------------------------------
        | Mock Dependencies
        |--------------------------------------------------------------------------
        */


        $this->gatewayResolver =
            $this->createMock(
                PaymentGatewayResolver::class
            );


        $this->orderService =
            $this->createMock(
                OrderService::class
            );


        $this->paymentService =
            $this->createMock(
                PaymentService::class
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

                $this->gatewayResolver,

                $this->orderService,

                $this->paymentService,

                $this->createMock(
                    StripeService::class
                ),

                $this->idempotencyService,

                $this->idempotencyRepository

            );

    }





    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */



    public function testCheckoutSuccess()
    {

        $this->idempotencyService
            ->expects($this->once())
            ->method('start')
            ->willReturn([

                'duplicate'=>false

            ]);



        $this->orderService
            ->expects($this->once())
            ->method('createOrder')
            ->willReturn([

                'id'=>100

            ]);



        $this->paymentService
            ->expects($this->once())
            ->method('createPayment')
            ->willReturn([

                'attempt_id'=>50

            ]);



        $gateway =
            $this->createMock(
                PaymentGatewayInterface::class
            );



        $this->gatewayResolver
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($gateway);



        $gateway
            ->expects($this->once())
            ->method('createPayment')
            ->willReturn([

                'success'=>true,

                'url'=>'payment-url'

            ]);



        $this->idempotencyRepository
            ->expects($this->once())
            ->method('complete');




        $result =
            $this->service
            ->checkout(

                1,

                [
                    [
                        'product_id'=>1
                    ]
                ],

                'stripe'

            );



        $this->assertTrue(
            $result['success']
        );


        $this->assertEquals(
            100,
            $result['order_id']
        );

    }







    /*
    |--------------------------------------------------------------------------
    | DUPLICATE IDEMPOTENCY
    |--------------------------------------------------------------------------
    */



    public function testDuplicateCheckoutReturnsCachedResponse()
    {


        $this->idempotencyService
            ->expects($this->once())
            ->method('start')
            ->willReturn([

                'duplicate'=>true,

                'response'=>[

                    'success'=>true,

                    'order_id'=>999

                ]

            ]);



        $result =
            $this->service
            ->checkout(

                1,

                [
                    [
                        'product_id'=>1
                    ]
                ],

                'stripe'

            );



        $this->assertEquals(

            999,

            $result['order_id']

        );

    }








    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */



    public function testGatewayFailureRollback()
    {

        $this->idempotencyService
            ->method('start')
            ->willReturn([

                'duplicate'=>false

            ]);



        $this->orderService
            ->method('createOrder')
            ->willReturn([

                'id'=>1

            ]);



        $this->paymentService
            ->method('createPayment')
            ->willReturn([

                'attempt_id'=>10

            ]);



        $gateway =
            $this->createMock(
                PaymentGatewayInterface::class
            );



        $this->gatewayResolver
            ->method('resolve')
            ->willReturn($gateway);



        $gateway
            ->method('createPayment')
            ->willReturn([

                'success'=>false,

                'message'=>'Payment failed'

            ]);



        $this->idempotencyRepository
            ->expects($this->once())
            ->method('fail');



        $this->expectException(
            Exception::class
        );



        $this->service
            ->checkout(

                1,

                [
                    [
                        'product_id'=>1
                    ]
                ],

                'stripe'

            );

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */



    public function testInvalidUser()
    {

        $this->expectException(
            Exception::class
        );


        $this->service
            ->checkout(

                0,

                [
                    [
                        'product_id'=>1
                    ]
                ],

                'stripe'

            );

    }





    public function testEmptyCart()
    {

        $this->expectException(
            Exception::class
        );


        $this->service
            ->checkout(

                1,

                [],

                'stripe'

            );

    }







    public function testMissingIdempotencyKey()
    {

        $GLOBALS['CI']->input =
            new class {

                public function post($key)
                {
                    return null;
                }

            };



        $this->expectException(
            Exception::class
        );



        $this->service
            ->checkout(

                1,

                [
                    [
                        'product_id'=>1
                    ]
                ],

                'stripe'

            );

    }







    /*
    |--------------------------------------------------------------------------
    | EDGE CASE
    |--------------------------------------------------------------------------
    */



    public function testMultipleProductsCart()
    {


        $this->idempotencyService
            ->method('start')
            ->willReturn([

                'duplicate'=>false

            ]);



        $this->orderService
            ->expects($this->once())
            ->method('createOrder')
            ->with(

                1,

                [
                    [
                        'product_id'=>1
                    ],

                    [
                        'product_id'=>2
                    ]

                ]

            )
            ->willReturn([

                'id'=>200

            ]);



        $this->paymentService
            ->method('createPayment')
            ->willReturn([

                'attempt_id'=>500

            ]);



        $gateway =
            $this->createMock(
                PaymentGatewayInterface::class
            );



        $this->gatewayResolver
            ->method('resolve')
            ->willReturn($gateway);



        $gateway
            ->method('createPayment')
            ->willReturn([

                'success'=>true,

                'url'=>'stripe-url'

            ]);



        $this->idempotencyRepository
            ->expects($this->once())
            ->method('complete');



        $result =
            $this->service
            ->checkout(

                1,

                [

                    [
                        'product_id'=>1
                    ],

                    [
                        'product_id'=>2
                    ]

                ],

                'stripe'

            );



        $this->assertTrue(
            $result['success']
        );


        $this->assertEquals(

            200,

            $result['order_id']

        );

    }
 /*
|--------------------------------------------------------------------------
| BUSINESS LOGIC RULES
|--------------------------------------------------------------------------
*/


public function testPaymentCannotBeCreatedBeforeOrder()
{

    /*
    |--------------------------------------------------------------------------
    | Idempotency started
    |--------------------------------------------------------------------------
    */

    $this->idempotencyService
        ->expects($this->once())
        ->method('start')
        ->willReturn([
            'duplicate'=>false
        ]);



    /*
    |--------------------------------------------------------------------------
    | Order creation fails
    |--------------------------------------------------------------------------
    */

    $this->orderService
        ->expects($this->once())
        ->method('createOrder')
        ->willThrowException(
            new Exception(
                'Order creation failed'
            )
        );



    /*
    |--------------------------------------------------------------------------
    | Payment must NEVER happen
    |--------------------------------------------------------------------------
    */

    $this->paymentService
        ->expects($this->never())
        ->method('createPayment');



    /*
    |--------------------------------------------------------------------------
    | Gateway must NEVER happen
    |--------------------------------------------------------------------------
    */

    $this->gatewayResolver
        ->expects($this->never())
        ->method('resolve');



    /*
    |--------------------------------------------------------------------------
    | Idempotency failure recorded
    |--------------------------------------------------------------------------
    */

    $this->idempotencyRepository
        ->expects($this->once())
        ->method('fail')
        ->with(
            'test-key-123',
            'Order creation failed'
        );



    $this->expectException(
        Exception::class
    );



    $this->service
        ->checkout(
            1,
            [
                [
                    'product_id'=>1
                ]
            ],
            'stripe'
        );

}
public function testGatewayCannotRunWithoutPayment()
{

    $this->idempotencyService
        ->method('start')
        ->willReturn([
            'duplicate'=>false
        ]);


    $this->orderService
        ->method('createOrder')
        ->willReturn([
            'id'=>1
        ]);


    $this->paymentService
        ->method('createPayment')
        ->willThrowException(
            new Exception(
                'Payment creation failed'
            )
        );


    $this->gatewayResolver
        ->expects($this->never())
        ->method('resolve');



    $this->idempotencyRepository
        ->expects($this->once())
        ->method('fail');



    $this->expectException(
        Exception::class
    );


    $this->service
        ->checkout(
            1,
            [
                [
                    'product_id'=>1
                ]
            ],
            'stripe'
        );

}
}