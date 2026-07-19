<?php

use PHPUnit\Framework\TestCase;


class LookupServiceTest extends TestCase
{

    protected $repository;
    protected $auditService;
    protected $service;



    protected function setUp(): void
    {

        $this->repository =
            $this->createMock(
                LookupRepositoryInterface::class
            );


        $this->auditService =
            $this->createMock(
                AuditLogService::class
            );


        $this->service =
            new LookupService(
                $this->repository,
                $this->auditService
            );

    }



    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    // SUCCESS: Create lookup
    public function test_create_lookup_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['group_id'] === 1 &&
                        $data['code'] === 'ACTIVE' &&
                        $data['value'] === 'Active' &&
                        $data['sort_order'] === 1 &&
                        $data['is_active'] === 1 &&
                        isset($data['created_at']);

                })
            )
            ->willReturn(10);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $result =
            $this->service->create([

                'group_id'=>1,
                'code'=>'ACTIVE',
                'value'=>'Active',
                'sort_order'=>1,
                'is_active'=>1

            ]);



        $this->assertEquals(
            10,
            $result
        );

    }





    // SUCCESS: Update lookup
    public function test_update_lookup_success()
    {

        $old =
            (object)[

                'code'=>'OLD',
                'value'=>'Old',
                'description'=>'Old description',
                'sort_order'=>1,
                'is_active'=>1

            ];



        $this->repository
            ->method('find')
            ->willReturn($old);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function($data){

                    return
                        $data['value']=='New' &&
                        isset($data['updated_at']);

                })
            )
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $result =
            $this->service->update(
                1,
                [
                    'value'=>'New'
                ]
            );


        $this->assertTrue($result);

    }





    // SUCCESS: Delete lookup
    public function test_delete_lookup_success()
    {

        $this->repository
            ->method('find')
            ->willReturn(
                (object)[
                    'id'=>1
                ]
            );


        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $this->assertTrue(

            $this->service
                ->delete(1)

        );

    }





    // SUCCESS: Get lookup by group
    public function test_get_by_group_success()
    {

        $data = [

            (object)[
                'id'=>1
            ]

        ];



        $this->repository
            ->expects($this->once())
            ->method('getByGroup')
            ->with(1)
            ->willReturn($data);



        $this->assertEquals(
            $data,
            $this->service
                ->getByGroup(1)
        );

    }





    // SUCCESS: Find lookup
    public function test_find_lookup_success()
    {

        $lookup =
            (object)[
                'id'=>1
            ];



        $this->repository
            ->method('find')
            ->with(1)
            ->willReturn($lookup);



        $this->assertEquals(
            $lookup,
            $this->service
                ->find(1)
        );

    }





    // SUCCESS: Count lookup by group
    public function test_count_by_group_success()
    {

        $this->repository
            ->method('countByGroup')
            ->with(1)
            ->willReturn(5);



        $this->assertEquals(
            5,
            $this->service
                ->countByGroup(1)
        );

    }





    // SUCCESS: Get lookup by group code
    public function test_get_by_group_code_success()
    {

        $data = [

            (object)[
                'code'=>'ACTIVE'
            ]

        ];



        $this->repository
            ->method('getByGroupCode')
            ->with('STATUS')
            ->willReturn($data);



        $this->assertEquals(
            $data,
            $this->service
                ->getByGroupCode('STATUS')
        );

    }






    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    // FAILURE: Repository create failed
    public function test_create_lookup_failed()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $result =
            $this->service->create([

                'code'=>'ACTIVE',
                'value'=>'Active'

            ]);



        $this->assertFalse($result);

    }





    // FAILURE: Update record not found
    public function test_update_lookup_not_found()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $this->repository
            ->expects($this->never())
            ->method('update');



        $this->assertFalse(

            $this->service
                ->update(
                    99,
                    []
                )

        );

    }





    // FAILURE: Update failed
    public function test_update_lookup_failed()
    {

        $this->repository
            ->method('find')
            ->willReturn(

                (object)[

                    'code'=>'A',
                    'value'=>'B',
                    'description'=>'C',
                    'sort_order'=>1,
                    'is_active'=>1

                ]

            );



        $this->repository
            ->method('update')
            ->willReturn(false);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $this->assertFalse(

            $this->service
                ->update(
                    1,
                    []
                )

        );

    }





    // FAILURE: Delete failed
    public function test_delete_lookup_failed()
    {

        $this->repository
            ->method('find')
            ->willReturn(
                (object)[
                    'id'=>1
                ]
            );


        $this->repository
            ->method('delete')
            ->willReturn(false);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $this->assertFalse(

            $this->service
                ->delete(1)

        );

    }







    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    // VALIDATION: Code required
    public function test_create_lookup_without_code()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'value'=>'Active'

        ]);

    }





    // VALIDATION: Value required
    public function test_create_lookup_without_value()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'code'=>'ACTIVE'

        ]);

    }





    // VALIDATION: Invalid active status
    public function test_create_lookup_invalid_active_status()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'code'=>'ACTIVE',
            'value'=>'Active',
            'is_active'=>5

        ]);

    }





    // VALIDATION: Negative sort order
    public function test_create_lookup_negative_sort_order()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'code'=>'ACTIVE',
            'value'=>'Active',
            'sort_order'=>-1

        ]);

    }






    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    // EDGE: Empty group result
    public function test_get_by_group_empty()
    {

        $this->repository
            ->method('getByGroup')
            ->willReturn([]);



        $this->assertEmpty(

            $this->service
                ->getByGroup(99)

        );

    }





    // EDGE: Lookup not found
    public function test_find_lookup_not_found()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $this->assertNull(

            $this->service
                ->find(999)

        );

    }





    // EDGE: Group code not found
    public function test_get_by_group_code_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('getByGroupCode')
            ->with('UNKNOWN')
            ->willReturn(null);



        $this->assertNull(

            $this->service
                ->getByGroupCode('UNKNOWN')

        );

    }





    /*
    |--------------------------------------------------------------------------
    | BUSINESS RULES
    |--------------------------------------------------------------------------
    */


    // BUSINESS RULE: Create logs audit
    public function test_create_lookup_logs_action()
    {

        $this->repository
            ->method('create')
            ->willReturn(1);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $this->service->create([

            'code'=>'ACTIVE',
            'value'=>'Active'

        ]);

    }





    // BUSINESS RULE: Update logs audit
    public function test_update_lookup_logs_action()
    {

        $this->repository
            ->method('find')
            ->willReturn(

                (object)[

                    'code'=>'OLD',
                    'value'=>'Old',
                    'description'=>'',
                    'sort_order'=>0,
                    'is_active'=>1

                ]

            );


        $this->repository
            ->method('update')
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $this->service->update(
            1,
            [
                'value'=>'New'
            ]
        );

    }





    // BUSINESS RULE: Delete logs audit
    public function test_delete_lookup_logs_action()
    {

        $this->repository
            ->method('find')
            ->willReturn(
                (object)[
                    'id'=>1
                ]
            );


        $this->repository
            ->method('delete')
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $this->service->delete(1);

    }

}