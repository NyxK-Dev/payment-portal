<?php

use PHPUnit\Framework\TestCase;


class AccountingServiceTest extends TestCase
{

    protected $invoiceRepository;
    protected $receiptRepository;
    protected $lookupRepository;
    protected $service;



    protected function setUp(): void
    {

        $this->invoiceRepository =
            $this->createMock(
                InvoiceRepositoryInterface::class
            );


        $this->receiptRepository =
            $this->createMock(
                ReceiptRepositoryInterface::class
            );


        $this->lookupRepository =
            $this->createMock(
                LookupRepositoryInterface::class
            );



        $this->service =
            new AccountingService(
                $this->invoiceRepository,
                $this->receiptRepository,
                $this->lookupRepository
            );
    }




    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_create_pending_invoice_success()
    {

        $status = (object)[
            'id'=>1
        ];



        $this->lookupRepository
            ->expects($this->once())
            ->method('findByGroupAndCode')
            ->with(
                6,
                'pending'
            )
            ->willReturn($status);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['order_id']==100
                        &&
                        $data['amount']==500
                        &&
                        $data['status_lookup_id']==1
                        &&
                        str_starts_with(
                            $data['invoice_no'],
                            'INV-'
                        );

                })
            )
            ->willReturn(10);



        $result =
            $this->service
                ->createPendingInvoice([
                    'id'=>100,
                    'total'=>500
                ]);



        $this->assertEquals(
            10,
            $result
        );

    }





    public function test_fulfill_invoice_creates_invoice_when_missing()
    {

        $paid = (object)[
            'id'=>2
        ];



        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($paid);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('findByOrderId')
            ->with(100)
            ->willReturn(null);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(50);



        $this->receiptRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['invoice_id']==50
                        &&
                        $data['amount']==500
                        &&
                        str_starts_with(
                            $data['receipt_no'],
                            'RCT-'
                        );

                })
            );



        $this->service
            ->fulfillInvoiceAndReceipt(
                100,
                500
            );


        $this->assertTrue(true);

    }





    public function test_fulfill_invoice_updates_existing_invoice()
    {

        $paid=(object)[
            'id'=>2
        ];


        $invoice=(object)[
            'id'=>99
        ];



        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($paid);



        $this->invoiceRepository
            ->method('findByOrderId')
            ->willReturn($invoice);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                99,
                $this->callback(function($data){

                    return
                        $data['status_lookup_id']==2
                        &&
                        isset($data['updated_at']);

                })
            );



        $this->receiptRepository
            ->expects($this->once())
            ->method('create');



        $this->service
            ->fulfillInvoiceAndReceipt(
                100,
                500
            );



        $this->assertTrue(true);

    }






    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    public function test_create_pending_invoice_repository_failure()
    {

        $status=(object)[
            'id'=>1
        ];


        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($status);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(0);



        $result =
            $this->service
            ->createPendingInvoice([
                'id'=>1,
                'total'=>100
            ]);



        $this->assertEquals(
            0,
            $result
        );

    }






public function test_fulfill_invoice_create_failed()
{

    $paidStatus = (object)[
        'id' => 2
    ];


    $this->lookupRepository
        ->method('findByGroupAndCode')
        ->willReturn($paidStatus);



    $this->invoiceRepository
        ->method('findByOrderId')
        ->willReturn(null);



    $this->invoiceRepository
        ->expects($this->once())
        ->method('create')
        ->willReturn(false);



    $this->receiptRepository
        ->expects($this->never())
        ->method('create');



    $this->expectException(Exception::class);


    $this->service
        ->fulfillInvoiceAndReceipt(
            100,
            500
        );
}

    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


 public function test_create_pending_invoice_missing_order_id()
{

    $this->expectException(
        InvalidArgumentException::class
    );


    $this->service
        ->createPendingInvoice([
            'total'=>500
        ]);

}




   public function test_create_pending_invoice_missing_total()
{

    $this->expectException(
        InvalidArgumentException::class
    );


    $this->service
        ->createPendingInvoice([
            'id'=>100
        ]);

}





    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    public function test_zero_amount_invoice()
    {

        $status=(object)[
            'id'=>1
        ];



        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($status);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['amount']==0;

                })
            )
            ->willReturn(1);



        $result =
            $this->service
            ->createPendingInvoice([
                'id'=>1,
                'total'=>0
            ]);



        $this->assertEquals(
            1,
            $result
        );

    }





    public function test_large_amount_invoice()
    {

        $status=(object)[
            'id'=>1
        ];



        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($status);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $result =
            $this->service
            ->createPendingInvoice([
                'id'=>1,
                'total'=>999999999
            ]);



        $this->assertEquals(
            1,
            $result
        );

    }







    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */


    public function test_lookup_status_called_before_invoice_creation()
    {

        $sequence=[];


        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturnCallback(function() use (&$sequence){

                $sequence[]='lookup';

                return (object)[
                    'id'=>1
                ];

            });



        $this->invoiceRepository
            ->method('create')
            ->willReturnCallback(function() use (&$sequence){

                $sequence[]='invoice';

                return 1;

            });



        $this->service
            ->createPendingInvoice([
                'id'=>1,
                'total'=>100
            ]);



        $this->assertEquals(
            [
                'lookup',
                'invoice'
            ],
            $sequence
        );

    }







    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */


    public function test_paid_invoice_creates_paid_receipt()
    {

        $paid=(object)[
            'id'=>5
        ];



        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($paid);



        $this->invoiceRepository
            ->method('findByOrderId')
            ->willReturn(null);



        $this->invoiceRepository
            ->method('create')
            ->willReturn(20);



        $this->receiptRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['status_lookup_id']==5;

                })
            );



        $this->service
            ->fulfillInvoiceAndReceipt(
                10,
                500
            );

    }


}