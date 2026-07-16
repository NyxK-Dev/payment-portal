<?php

use PHPUnit\Framework\TestCase;

class InvoiceServiceTest extends TestCase
{

    protected $repository;

    protected $auditService;

    protected $service;



    protected function setUp(): void
    {

        $this->repository =
            $this->createMock(
                InvoiceRepositoryInterface::class
            );


        $this->auditService =
            $this->createMock(
                AuditLogService::class
            );


        $this->service =
            new InvoiceService(
                $this->repository,
                $this->auditService
            );
    }




    public function test_get_filtered_invoices()
    {

        $invoice = (object)[

            'id' => 1,

            'amount' => 500,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $this->repository
            ->expects($this->once())
            ->method('getFilteredInvoices')
            ->with([
                'status' => 'paid'
            ])
            ->willReturn([

                $invoice

            ]);



        $result =
            $this->service
            ->getFilteredInvoices([
                'status' => 'paid'
            ]);



        $this->assertCount(
            1,
            $result
        );


        $this->assertEquals(
            '500.00',
            $result[0]->formatted_amount
        );


        $this->assertEquals(
            'bg-secondary',
            $result[0]->badge_class
        );
    }

    public function test_get_invoice_details_with_items()
    {

        $invoice = (object)[

            'id' => 10,

            'order_id' => 100,

            'amount' => 1000,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $items = [

            (object)[

                'quantity' => 2,

                'unit_price' => 100

            ],

            (object)[

                'quantity' => 3,

                'unit_price' => 200

            ]

        ];




        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn(
                $invoice
            );



        $this->repository
            ->expects($this->once())
            ->method('getOrderItems')
            ->with(100)
            ->willReturn(
                $items
            );



        $result =
            $this->service
            ->getInvoiceDetailsWithItems(10);




        $this->assertEquals(
            '800.00',
            $result->subtotal_aggregate
        );



        // number_format(1000, 2)
        // returns 1,000.00
        $this->assertEquals(
            '1,000.00',
            $result->formatted_total_due
        );



        $this->assertCount(
            2,
            $result->items
        );


        $this->assertEquals(
            '100.00',
            $result->items[0]->formatted_unit_price
        );


        $this->assertEquals(
            '200.00',
            $result->items[1]->formatted_unit_price
        );


        $this->assertEquals(
            '200.00',
            $result->items[0]->formatted_line_total
        );


        $this->assertEquals(
            '600.00',
            $result->items[1]->formatted_line_total
        );
    }
    public function test_get_customer_invoices()
    {


        $invoice = (object)[

            'amount' => 200,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => 'bg-success'

        ];



        $this->repository
            ->expects($this->once())
            ->method('getByUser')
            ->with(5)
            ->willReturn([

                $invoice

            ]);



        $result =
            $this->service
            ->getCustomerInvoices(5);



        $this->assertCount(
            1,
            $result
        );


        $this->assertEquals(
            '200.00',
            $result[0]->formatted_amount
        );
    }

    public function test_get_customer_invoice_not_found()
    {


        $this->repository
            ->expects($this->once())
            ->method('findByUser')
            ->with(
                99,
                5
            )
            ->willReturn(null);



        $result =
            $this->service
            ->getCustomerInvoice(
                99,
                5
            );



        $this->assertNull(
            $result
        );
    }

    public function test_create_invoice()
    {


        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['order_id'] === 1 &&
                        $data['invoice_no'] === 'INV-001' &&
                        $data['amount'] === 500;
                })
            )
            ->willReturn(10);



        $result =
            $this->service
            ->create([

                'order_id' => 1,

                'invoice_no' => 'INV-001',

                'amount' => 500,

                'status_lookup_id' => 2,

                'issued_by' => 1

            ]);



        $this->assertEquals(
            10,
            $result
        );
    }

    public function test_update_invoice()
    {


        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function ($data) {

                    return
                        $data['amount'] === 900 &&
                        $data['status_lookup_id'] === 2;
                })
            )
            ->willReturn(true);



        $result =
            $this->service
            ->update(
                10,
                [

                    'amount' => 900,

                    'status_lookup_id' => 2

                ]
            );



        $this->assertTrue(
            $result
        );
    }

    public function test_delete_invoice()
    {


        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->delete(10)

        );
    }
    public function test_get_invoice_details_not_found()
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn(null);

        $result = $this->service
            ->getInvoiceDetailsWithItems(10);

        $this->assertNull($result);
    }
    public function test_get_customer_invoice_success()
    {
        $invoice = (object)[
            'id' => 1,
            'order_id' => 100,
            'amount' => 600,
            'created_at' => '2026-07-13 10:00:00',
            'badge_class' => null
        ];

        $items = [
            (object)[
                'quantity' => 2,
                'unit_price' => 100
            ]
        ];

        $this->repository
            ->expects($this->once())
            ->method('findByUser')
            ->with(1, 5)
            ->willReturn($invoice);

        $this->repository
            ->expects($this->once())
            ->method('getOrderItems')
            ->with(100)
            ->willReturn($items);

        $result = $this->service
            ->getCustomerInvoice(1, 5);

        $this->assertEquals(
            '600.00',
            $result->formatted_total_due
        );
    }
    public function test_delete_invoice_failed()
    {
        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(false);

        $this->assertFalse(
            $this->service->delete(10)
        );
    }
    public function test_update_invoice_failed()
    {
        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function ($data) {
                    return $data['amount'] === 900
                        && $data['status_lookup_id'] === 2;
                })
            )
            ->willReturn(false);

        $result = $this->service->update(
            10,
            [
                'amount' => 900,
                'status_lookup_id' => 2
            ]
        );

        $this->assertFalse($result);
    }
}
