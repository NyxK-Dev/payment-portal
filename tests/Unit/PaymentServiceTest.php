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


        /*
        |--------------------------------------------------------------------------
        | Mock Database
        |--------------------------------------------------------------------------
        */

        $CI->db =
            $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'trans_begin',
                'trans_commit',
                'trans_rollback',
                'get_where'
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
        | Mock Repositories
        |--------------------------------------------------------------------------
        */


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

    public function test_create_payment()
    {


        $order = [

            'id' => 100,

            'total' => 50

        ];



        $this->paymentRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(
                10
            );



        $this->accountingService
            ->expects($this->once())
            ->method('createPendingInvoice')
            ->with(
                $order
            )
            ->willReturn(
                20
            );



        $this->paymentAttemptRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(
                30
            );



        $result =
            $this->service
            ->createPayment(
                $order
            );



        $this->assertEquals(
            10,
            $result['id']
        );


        $this->assertEquals(
            30,
            $result['attempt_id']
        );


        $this->assertEquals(
            20,
            $result['invoice_id']
        );
    }

    public function test_save_stripe_session()
    {


        $this->paymentAttemptRepository
            ->expects($this->once())
            ->method('update')
            ->with(

                30,

                $this->arrayHasKey(
                    'stripe_session_id'
                )

            )
            ->willReturn(
                true
            );



        $result =
            $this->service
            ->saveStripeSession(
                30,
                'sess_123'
            );



        $this->assertTrue(
            $result
        );
    }

    public function test_webhook_exists()
    {


        $this->stripeWebhookEventRepository
            ->expects($this->once())
            ->method('existsByEventId')
            ->with(
                'evt_123'
            )
            ->willReturn(
                true
            );



        $result =
            $this->service
            ->webhookExists(
                'evt_123'
            );


        $this->assertTrue(
            $result
        );
    }

    public function test_save_webhook_event()
    {


        $event = new stdClass();


        $event->id =
            'evt_123';


        $event->type =
            'payment.success';



        $this->stripeWebhookEventRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(
                true
            );



        $result =
            $this->service
            ->saveWebhookEvent(
                $event
            );



        $this->assertTrue(
            $result
        );
    }
    public function test_save_stripe_session_failed()
    {
        $this->paymentAttemptRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->saveStripeSession(
                30,
                'sess_123'
            )
        );
    }
    public function test_webhook_not_exists()
    {
        $this->stripeWebhookEventRepository
            ->expects($this->once())
            ->method('existsByEventId')
            ->with('evt_123')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->webhookExists('evt_123')
        );
    }
    public function test_save_webhook_event_failed()
    {
        $event = new stdClass();
        $event->id = 'evt_123';
        $event->type = 'payment.success';

        $this->stripeWebhookEventRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->saveWebhookEvent($event)
        );
    }
    public function test_mark_webhook_processed()
    {
        $this->stripeWebhookEventRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->arrayHasKey('processed')
            )
            ->willReturn(true);

        $this->assertTrue(
            $this->service->markWebhookProcessed(1)
        );
    }
    public function test_mark_webhook_processed_failed()
    {
        $this->stripeWebhookEventRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);

        $this->assertFalse(
            $this->service->markWebhookProcessed(1)
        );
    }
}
