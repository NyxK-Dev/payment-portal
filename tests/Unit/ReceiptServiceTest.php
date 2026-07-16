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



    public function test_get_all_with_relations()
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




    public function test_find_with_relations()
    {

        $receipt = (object)[
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




    public function test_get_customer_receipts()
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





    public function test_get_customer_receipt()
    {

        $receipt = (object)[
            'id' => 10
        ];


        $this->repository
            ->expects($this->once())
            ->method('findByUser')
            ->with(
                10,
                5
            )
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







    public function test_create_receipt()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['invoice_id'] === 100 &&
                        $data['receipt_no'] === 'RCT-001' &&
                        $data['amount'] === 500 &&
                        $data['status_lookup_id'] === 2 &&
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







    public function test_create_receipt_with_custom_issued_at()
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

                'invoice_id' => 100,
                'receipt_no' => 'RCT-001',
                'amount' => 500,
                'status_lookup_id' => 2,
                'issued_by' => 1,
                'issued_at' => '2026-07-01 10:00:00'

            ]);
    }







    public function test_update_receipt()
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







    public function test_update_receipt_failed()
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



        $this->assertFalse(
            $result
        );
    }







    public function test_delete_receipt()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(10)
            ->willReturn(true);



        $result =
            $this->service
            ->delete(10);



        $this->assertTrue(
            $result
        );
    }







    public function test_delete_receipt_failed()
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
    public function test_find_with_relations_not_found()
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
    public function test_get_customer_receipt_not_found()
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
    public function test_create_receipt_failed()
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
}
