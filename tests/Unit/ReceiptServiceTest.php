<?php

use PHPUnit\Framework\TestCase;


class ReceiptServiceTest extends TestCase
{

    protected $repository;
    protected $auditService;
    protected $service;


    protected function setUp(): void
    {
        $this->repository =
            $this->createMock(
                ReceiptRepositoryInterface::class
            );


        $this->auditService =
            $this->createMock(
                AuditLogService::class
            );


        $this->service =
            new ReceiptService(
                $this->repository,
                $this->auditService
            );
    }



    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    // SUCCESS: Get all receipts with relations
    public function test_get_all_with_relations_success()
    {
        $receipts = [
            (object)[
                'id' => 1,
                'receipt_no' => 'RCT-001'
            ]
        ];


        $this->repository
            ->expects($this->once())
            ->method('getAllWithRelations')
            ->willReturn($receipts);


        $result =
            $this->service
            ->getAllWithRelations();


        $this->assertEquals(
            $receipts,
            $result
        );
    }



    // SUCCESS: Find receipt with relations
    public function test_find_with_relations_success()
    {

        $receipt =
            (object)[
                'id' => 10
            ];


        $this->repository
            ->expects($this->once())
            ->method('findWithRelations')
            ->with(10)
            ->willReturn($receipt);


        $result =
            $this->service
            ->findWithRelations(10);


        $this->assertEquals(
            $receipt,
            $result
        );
    }



    // SUCCESS: Customer can get own receipts
    public function test_customer_get_receipts_success()
    {

        $receipts = [
            (object)[
                'id' => 1
            ]
        ];


        $this->repository
            ->expects($this->once())
            ->method('getByUser')
            ->with(5)
            ->willReturn($receipts);



        $result =
            $this->service
            ->getCustomerReceipts(5);



        $this->assertEquals(
            $receipts,
            $result
        );
    }



    // SUCCESS: Customer can get specific receipt
    public function test_customer_get_receipt_success()
    {

        $receipt =
            (object)[
                'id' => 10
            ];


        $this->repository
            ->expects($this->once())
            ->method('findByUser')
            ->with(10, 5)
            ->willReturn($receipt);



        $result =
            $this->service
            ->getCustomerReceipt(
                10,
                5
            );


        $this->assertEquals(
            $receipt,
            $result
        );
    }




    // SUCCESS: Create receipt
    public function test_create_receipt_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['invoice_id'] == 100 &&
                        $data['receipt_no'] == 'RCT-001' &&
                        $data['amount'] == 500 &&
                        $data['status_lookup_id'] == 2 &&
                        isset($data['created_at']);
                })
            )
            ->willReturn(1);



        $result =
            $this->service
            ->create([

                'invoice_id' => 100,
                'receipt_no' => 'RCT-001',
                'amount' => 500,
                'status_lookup_id' => 2,
                'issued_by' => 1

            ]);



        $this->assertEquals(
            1,
            $result
        );
    }




    // SUCCESS: Create receipt with custom issued date
    public function test_create_receipt_with_custom_issued_at()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['issued_at']
                        ==
                        '2026-07-01 10:00:00';
                })
            )
            ->willReturn(1);



        $this->service
            ->create([

                'invoice_id' => 100,
                'receipt_no' => 'RCT-001',
                'amount' => 500,
                'status_lookup_id' => 2,
                'issued_by' => 1,
                'issued_at' => '2026-07-01 10:00:00'

            ]);
    }




    // SUCCESS: Update receipt
    public function test_update_receipt_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function ($data) {

                    return
                        $data['amount'] == 900 &&
                        $data['status_lookup_id'] == 2 &&
                        isset($data['updated_at']);
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



        $this->assertTrue($result);
    }




    // SUCCESS: Delete receipt
    public function test_delete_receipt_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(true);



        $result =
            $this->service
            ->delete(10);



        $this->assertTrue($result);
    }




    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    // FAILURE: Receipt update failed
    public function test_update_receipt_failure()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);



        $result =
            $this->service
            ->update(
                10,
                [
                    'amount' => 900
                ]
            );



        $this->assertFalse($result);
    }




    // FAILURE: Receipt delete failed
    public function test_delete_receipt_failure()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(false);



        $result =
            $this->service
            ->delete(10);



        $this->assertFalse($result);
    }




    // FAILURE: Repository create failed
    public function test_create_receipt_failure()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);



        $result =
            $this->service
            ->create([

                'invoice_id' => 100,
                'receipt_no' => 'RCT-001',
                'amount' => 500,
                'status_lookup_id' => 2,
                'issued_by' => 1

            ]);



        $this->assertFalse($result);
    }




    /*
    |--------------------------------------------------------------------------
    | NOT FOUND CASES
    |--------------------------------------------------------------------------
    */


    // EDGE: Receipt not found by id
    public function test_find_receipt_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('findWithRelations')
            ->with(99)
            ->willReturn(null);



        $result =
            $this->service
            ->findWithRelations(99);



        $this->assertNull($result);
    }




    // EDGE: Customer receipt not found
    public function test_customer_receipt_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('findByUser')
            ->with(99, 5)
            ->willReturn(null);



        $result =
            $this->service
            ->getCustomerReceipt(
                99,
                5
            );



        $this->assertNull($result);
    }




    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    // VALIDATION: Missing invoice id
    // VALIDATION: Missing invoice id
    public function test_create_receipt_without_invoice_id()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'receipt_no' => 'RCT-001',
            'amount' => 500,
            'status_lookup_id' => 2,
            'issued_by' => 1

        ]);
    }




    // VALIDATION: Missing receipt number
    public function test_create_receipt_without_receipt_number()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'invoice_id' => 100,
            'amount' => 500,
            'status_lookup_id' => 2,
            'issued_by' => 1

        ]);
    }




    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    // EDGE: Create receipt with zero amount
    public function test_create_receipt_zero_amount()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $result =
            $this->service
            ->create([

                'invoice_id' => 100,
                'receipt_no' => 'RCT-001',
                'amount' => 0,
                'status_lookup_id' => 2,
                'issued_by' => 1

            ]);



        $this->assertEquals(
            1,
            $result
        );
    }




    // EDGE: Update receipt with empty data
    public function test_update_receipt_empty_data()
    {

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function ($data) {

                    return isset($data['updated_at']);
                })
            )
            ->willReturn(true);



        $result =
            $this->service
            ->update(
                10,
                []
            );



        $this->assertTrue($result);
    }




    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */


    // BUSINESS RULE: Service adds created_at automatically
    public function test_create_receipt_adds_created_at()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return isset(
                        $data['created_at']
                    );
                })
            )
            ->willReturn(1);



        $this->service
            ->create([

                'invoice_id' => 1,
                'receipt_no' => 'RCT-100',
                'amount' => 100,
                'status_lookup_id' => 2,
                'issued_by' => 1

            ]);
    }




    // BUSINESS RULE: Update always adds updated_at
    public function test_update_receipt_adds_updated_at()
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



        $this->service
            ->update(
                1,
                [
                    'amount' => 100
                ]
            );
    }
}
