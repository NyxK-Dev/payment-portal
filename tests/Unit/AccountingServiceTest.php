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




    /**
     * Test create pending invoice
     */
    public function test_create_pending_invoice()
    {

        $pendingStatus =
            (object)[
                'id' => 1
            ];



        $this->lookupRepository
            ->expects($this->once())
            ->method('findByGroupAndCode')
            ->with(
                6,
                'pending'
            )
            ->willReturn($pendingStatus);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['order_id'] === 100 &&
                        $data['amount'] === 500 &&
                        $data['status_lookup_id'] === 1 &&
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
                'id' => 100,
                'total' => 500
            ]);



        $this->assertEquals(
            10,
            $result
        );
    }


    /**
     * Test fulfill invoice when invoice does not exist
     */
    public function test_fulfill_invoice_create_new_invoice_and_receipt()
    {

        $paidStatus =
            (object)[
                'id' => 2
            ];



        $this->lookupRepository
            ->expects($this->once())
            ->method('findByGroupAndCode')
            ->with(
                6,
                'paid'
            )
            ->willReturn($paidStatus);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('findByOrderId')
            ->with(100)
            ->willReturn(null);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['order_id'] == 100 &&
                        $data['amount'] == 500 &&
                        $data['status_lookup_id'] == 2;
                })
            )
            ->willReturn(50);



        $this->receiptRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['invoice_id'] == 50 &&
                        $data['amount'] == 500 &&
                        $data['status_lookup_id'] == 2;
                })
            );



        $this->service
            ->fulfillInvoiceAndReceipt(
                100,
                500
            );



        $this->assertTrue(true);
    }






    /**
     * Test fulfill invoice when invoice already exists
     */
    public function test_fulfill_invoice_update_existing_invoice_and_create_receipt()
    {

        $paidStatus =
            (object)[
                'id' => 2
            ];



        $invoice =
            (object)[
                'id' => 99
            ];



        $this->lookupRepository
            ->expects($this->once())
            ->method('findByGroupAndCode')
            ->with(
                6,
                'paid'
            )
            ->willReturn($paidStatus);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('findByOrderId')
            ->with(100)
            ->willReturn($invoice);



        $this->invoiceRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                99,
                $this->callback(function ($data) {

                    return
                        $data['status_lookup_id'] == 2 &&
                        isset($data['updated_at']);
                })
            );



        $this->receiptRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['invoice_id'] == 99 &&
                        $data['amount'] == 500;
                })
            );



        $this->service
            ->fulfillInvoiceAndReceipt(
                100,
                500
            );



        $this->assertTrue(true);
    }
    public function test_create_pending_invoice_failed()
    {
        $pendingStatus = (object)['id' => 1];

        $this->lookupRepository
            ->method('findByGroupAndCode')
            ->willReturn($pendingStatus);

        $this->invoiceRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(0);

        $result = $this->service
            ->createPendingInvoice([
                'id' => 100,
                'total' => 500
            ]);

        $this->assertEquals(0, $result);
    }
}
