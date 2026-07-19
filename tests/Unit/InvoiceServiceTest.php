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





    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */



    // SUCCESS: Get filtered invoices
    public function test_get_filtered_invoices_success()
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







    // SUCCESS: Get invoice details with items
    public function test_get_invoice_details_with_items_success()
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
            ->willReturn($invoice);



        $this->repository
            ->expects($this->once())
            ->method('getOrderItems')
            ->with(100)
            ->willReturn($items);



        $result =
            $this->service
            ->getInvoiceDetailsWithItems(10);



        $this->assertEquals(
            '800.00',
            $result->subtotal_aggregate
        );


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
    }







    // SUCCESS: Get customer invoices
    public function test_get_customer_invoices_success()
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







    // SUCCESS: Get customer invoice details
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
            ->with(
                1,
                5
            )
            ->willReturn($invoice);



        $this->repository
            ->expects($this->once())
            ->method('getOrderItems')
            ->with(100)
            ->willReturn($items);



        $result =
            $this->service
            ->getCustomerInvoice(
                1,
                5
            );



        $this->assertEquals(
            '600.00',
            $result->formatted_total_due
        );
    }







    // SUCCESS: Create invoice
    public function test_create_invoice_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return

                        $data['order_id'] === 1 &&
                        $data['invoice_no'] === 'INV-001' &&
                        $data['amount'] === 500 &&
                        isset($data['created_at']) &&
                        isset($data['updated_at']);
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







    // SUCCESS: Update invoice
    public function test_update_invoice_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function ($data) {

                    return

                        $data['amount'] === 900 &&
                        $data['status_lookup_id'] === 2 &&
                        isset($data['updated_at']);
                })
            )
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->update(
                    10,
                    [
                        'amount' => 900,
                        'status_lookup_id' => 2
                    ]
                )

        );
    }







    // SUCCESS: Delete invoice
    public function test_delete_invoice_success()
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
    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    // FAILURE: Invoice not found
    public function test_get_invoice_details_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $result =
            $this->service
            ->getInvoiceDetailsWithItems(99);



        $this->assertNull(
            $result
        );
    }







    // FAILURE: Customer invoice not found
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







    // FAILURE: Update invoice failed
    public function test_update_invoice_failed()
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
            ->willReturn(false);



        $result =
            $this->service
            ->update(
                10,
                [

                    'amount' => 900,

                    'status_lookup_id' => 2

                ]
            );



        $this->assertFalse(
            $result
        );
    }







    // FAILURE: Delete invoice failed
    public function test_delete_invoice_failed()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(false);



        $result =
            $this->service
            ->delete(10);



        $this->assertFalse(
            $result
        );
    }







    // FAILURE: Create invoice failed
    public function test_create_invoice_failed()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);



        $result =
            $this->service
            ->create([

                'order_id' => 1,

                'invoice_no' => 'INV-001',

                'amount' => 500,

                'status_lookup_id' => 2,

                'issued_by' => 1

            ]);



        $this->assertFalse(
            $result
        );
    }








    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */



    // EDGE: Empty invoice list
    public function test_get_filtered_invoices_empty()
    {

        $this->repository
            ->expects($this->once())
            ->method('getFilteredInvoices')
            ->willReturn([]);



        $result =
            $this->service
            ->getFilteredInvoices();



        $this->assertEmpty(
            $result
        );
    }








    // EDGE: Empty customer invoices
    public function test_get_customer_invoices_empty()
    {

        $this->repository
            ->expects($this->once())
            ->method('getByUser')
            ->with(5)
            ->willReturn([]);



        $result =
            $this->service
            ->getCustomerInvoices(5);



        $this->assertEmpty(
            $result
        );
    }








    // EDGE: Invoice without items
    public function test_invoice_without_items()
    {

        $invoice = (object)[

            'order_id' => 100,

            'amount' => 500,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->willReturn($invoice);



        $this->repository
            ->expects($this->once())
            ->method('getOrderItems')
            ->with(100)
            ->willReturn([]);



        $result =
            $this->service
            ->getInvoiceDetailsWithItems(1);



        $this->assertEquals(

            '0.00',

            $result->subtotal_aggregate

        );



        $this->assertEmpty(

            $result->items

        );
    }








    // EDGE: Existing line total should not change
    public function test_invoice_item_existing_line_total()
    {

        $invoice = (object)[

            'order_id' => 100,

            'amount' => 500,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $items = [

            (object)[

                'quantity' => 2,

                'unit_price' => 100,

                'line_total' => 999

            ]

        ];



        $this->repository
            ->method('find')
            ->willReturn($invoice);



        $this->repository
            ->method('getOrderItems')
            ->willReturn($items);



        $result =
            $this->service
            ->getInvoiceDetailsWithItems(1);



        $this->assertEquals(

            '999.00',

            $result->subtotal_aggregate

        );
    }








    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */



    // RULE: get invoice should find before loading items
    public function test_invoice_details_repository_flow()
    {

        $invoice = (object)[

            'order_id' => 10,

            'amount' => 100,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($invoice);



        $this->repository
            ->expects($this->once())
            ->method('getOrderItems')
            ->with(10)
            ->willReturn([]);



        $this->service
            ->getInvoiceDetailsWithItems(1);
    }







    // RULE: Delete only calls repository delete
    public function test_delete_only_repository_call()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);



        $this->service
            ->delete(1);
    }







    // RULE: Update only sends allowed fields
    public function test_update_only_allowed_fields()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function ($data) {

                    return

                        isset($data['amount']) &&
                        isset($data['updated_at']) &&
                        !isset($data['invoice_no']);
                })
            )
            ->willReturn(true);



        $this->service
            ->update(
                1,
                [

                    'amount' => 300,

                    'invoice_no' => 'TEST'

                ]
            );
    }
    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */


    // BUSINESS: Invoice amount formatting
    public function test_invoice_amount_formatting()
    {

        $invoice = (object)[

            'amount' => 1234.5,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $this->repository
            ->expects($this->once())
            ->method('getFilteredInvoices')
            ->willReturn([

                $invoice

            ]);



        $result =
            $this->service
            ->getFilteredInvoices();



        $this->assertEquals(

            '1,234.50',

            $result[0]->formatted_amount

        );
    }








    // BUSINESS: Created date formatting
    public function test_invoice_created_date_formatting()
    {

        $invoice = (object)[

            'amount' => 100,

            'created_at' => '2026-07-13 15:30:00',

            'badge_class' => null

        ];



        $this->repository
            ->method('getFilteredInvoices')
            ->willReturn([

                $invoice

            ]);



        $result =
            $this->service
            ->getFilteredInvoices();



        $this->assertEquals(

            '2026-07-13 15:30',

            $result[0]->formatted_created_at

        );
    }








    // BUSINESS: Default badge class
    public function test_invoice_default_badge_class()
    {

        $invoice = (object)[

            'amount' => 500,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $this->repository
            ->method('getFilteredInvoices')
            ->willReturn([

                $invoice

            ]);



        $result =
            $this->service
            ->getFilteredInvoices();



        $this->assertEquals(

            'bg-secondary',

            $result[0]->badge_class

        );
    }








    // BUSINESS: Keep existing badge class
    public function test_invoice_existing_badge_class()
    {

        $invoice = (object)[

            'amount' => 500,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => 'bg-success'

        ];



        $this->repository
            ->method('getFilteredInvoices')
            ->willReturn([

                $invoice

            ]);



        $result =
            $this->service
            ->getFilteredInvoices();



        $this->assertEquals(

            'bg-success',

            $result[0]->badge_class

        );
    }








    // BUSINESS: Line total calculation
    public function test_invoice_item_line_total_calculation()
    {

        $invoice = (object)[

            'order_id' => 10,

            'amount' => 1000,

            'created_at' => '2026-07-13 10:00:00',

            'badge_class' => null

        ];



        $items = [

            (object)[

                'quantity' => 5,

                'unit_price' => 20

            ]

        ];



        $this->repository
            ->method('find')
            ->willReturn($invoice);



        $this->repository
            ->method('getOrderItems')
            ->willReturn($items);



        $result =
            $this->service
            ->getInvoiceDetailsWithItems(1);



        $this->assertEquals(

            '100.00',

            $result->items[0]->formatted_line_total

        );
    }








    // BUSINESS: Custom issued_at accepted
    public function test_create_invoice_custom_issued_at()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return

                        $data['issued_at']
                        ===
                        '2026-07-01 10:00:00';
                })
            )
            ->willReturn(1);



        $this->service
            ->create([

                'order_id' => 1,

                'invoice_no' => 'INV-001',

                'amount' => 500,

                'status_lookup_id' => 2,

                'issued_by' => 1,

                'issued_at' => '2026-07-01 10:00:00'

            ]);
    }








    // BUSINESS: Default issued_at generated
    public function test_create_invoice_default_issued_at()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return isset(
                        $data['issued_at']
                    );
                })
            )
            ->willReturn(1);



        $this->service
            ->create([

                'order_id' => 1,

                'invoice_no' => 'INV-001',

                'amount' => 500,

                'status_lookup_id' => 2,

                'issued_by' => 1

            ]);
    }








    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    // VALIDATION: Missing order id
    public function test_create_invoice_without_order_id()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service
            ->create([

                'invoice_no' => 'INV-001',

                'amount' => 100,

                'status_lookup_id' => 2,

                'issued_by' => 1

            ]);
    }
    // VALIDATION: Missing invoice number
    public function test_create_invoice_without_invoice_number()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service
            ->create([

                'order_id' => 1,

                'amount' => 100,

                'status_lookup_id' => 2,

                'issued_by' => 1

            ]);
    }
    // VALIDATION: Missing amount
    public function test_create_invoice_without_amount()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service
            ->create([

                'order_id' => 1,

                'invoice_no' => 'INV-001',

                'status_lookup_id' => 2,

                'issued_by' => 1

            ]);
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE EDGE CASES
    |--------------------------------------------------------------------------
    */


    // EDGE: Empty update data
    public function test_update_invoice_empty_data()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function ($data) {

                    return isset(
                        $data['updated_at']
                    );
                })
            )
            ->willReturn(true);



        $this->assertTrue(

            $this->service
                ->update(
                    1,
                    []
                )

        );
    }








    // EDGE: Update ignored unknown fields
    public function test_update_invoice_ignore_unknown_fields()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function ($data) {

                    return

                        !isset($data['invoice_no']) &&
                        isset($data['updated_at']);
                })
            )
            ->willReturn(true);



        $this->service
            ->update(
                1,
                [

                    'invoice_no' => 'TEST'

                ]
            );
    }
}
