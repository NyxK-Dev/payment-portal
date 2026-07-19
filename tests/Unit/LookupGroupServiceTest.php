<?php

use PHPUnit\Framework\TestCase;


class LookupGroupServiceTest extends TestCase
{

    protected $repository;
    protected $auditService;
    protected $service;



    protected function setUp(): void
    {

        $this->repository =
            $this->createMock(
                LookupGroupRepositoryInterface::class
            );


        $this->auditService =
            $this->createMock(
                AuditLogService::class
            );


        $this->service =
            new LookupGroupService(
                $this->repository,
                $this->auditService
            );

    }





    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    // SUCCESS: Create lookup group
    public function test_create_lookup_group_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['code']==='PAYMENT' &&
                        $data['name']==='Payment' &&
                        isset($data['created_at']);

                })
            )
            ->willReturn(10);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $result =
            $this->service->create([

                'code'=>'PAYMENT',
                'name'=>'Payment',
                'description'=>'Payment status'

            ]);



        $this->assertEquals(
            10,
            $result
        );

    }






    // SUCCESS: Update lookup group
    public function test_update_lookup_group_success()
    {

        $oldRecord =
            (object)[

                'code'=>'OLD',
                'name'=>'Old Name',
                'description'=>'Old'

            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($oldRecord);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function($data){

                    return
                        $data['code']==='NEW' &&
                        $data['name']==='New Name' &&
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
                    'code'=>'NEW',
                    'name'=>'New Name'
                ]
            );



        $this->assertTrue($result);

    }







    // SUCCESS: Delete lookup group
    public function test_delete_lookup_group_success()
    {

        $oldRecord =
            (object)[

                'id'=>1,
                'name'=>'Payment'

            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->willReturn($oldRecord);



        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $this->assertTrue(

            $this->service
                ->delete(1)

        );

    }







    // SUCCESS: Get all lookup groups
    public function test_get_all_lookup_groups_success()
    {

        $groups = [

            (object)[
                'id'=>1,
                'code'=>'PAYMENT'
            ]

        ];



        $this->repository
            ->expects($this->once())
            ->method('all')
            ->willReturn($groups);



        $this->assertEquals(

            $groups,

            $this->service
                ->getAll()

        );

    }







    // SUCCESS: Find lookup group
    public function test_find_lookup_group_success()
    {

        $group =
            (object)[
                'id'=>1
            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($group);



        $this->assertEquals(

            $group,

            $this->service
                ->find(1)

        );

    }







    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */


    // FAILURE: Create lookup group failed
    public function test_create_lookup_group_failed()
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

                'code'=>'TEST',
                'name'=>'Test'

            ]);



        $this->assertFalse($result);

    }







    // FAILURE: Update lookup group not found
    public function test_update_lookup_group_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $this->repository
            ->expects($this->never())
            ->method('update');



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $this->assertFalse(

            $this->service
                ->update(
                    99,
                    [
                        'name'=>'Test'
                    ]
                )

        );

    }







    // FAILURE: Update repository failed
    public function test_update_lookup_group_failed()
    {

        $this->repository
            ->method('find')
            ->willReturn(

                (object)[

                    'code'=>'OLD',
                    'name'=>'Old',
                    'description'=>'Old'

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
                    [
                        'name'=>'New'
                    ]
                )

        );

    }







    // FAILURE: Delete repository failed
    public function test_delete_lookup_group_failed()
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
    public function test_create_lookup_group_without_code()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'name'=>'Payment'

        ]);

    }







    // VALIDATION: Name required
    public function test_create_lookup_group_without_name()
    {

        $this->expectException(
            InvalidArgumentException::class
        );


        $this->service->create([

            'code'=>'PAYMENT'

        ]);

    }







    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    // EDGE: Find missing lookup group
    public function test_find_lookup_group_not_found()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $this->assertNull(

            $this->service
                ->find(99)

        );

    }







    // EDGE: Empty lookup group list
    public function test_get_all_lookup_groups_empty()
    {

        $this->repository
            ->method('all')
            ->willReturn([]);



        $this->assertEmpty(

            $this->service
                ->getAll()

        );

    }








    /*
    |--------------------------------------------------------------------------
    | BUSINESS RULES
    |--------------------------------------------------------------------------
    */


    // BUSINESS RULE: Create adds created_at
    public function test_create_lookup_group_adds_created_at()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return isset(
                        $data['created_at']
                    );

                })
            )
            ->willReturn(1);



        $this->service->create([

            'code'=>'TEST',
            'name'=>'Test'

        ]);

    }







    // BUSINESS RULE: Update adds updated_at
    public function test_update_lookup_group_adds_updated_at()
    {

        $this->repository
            ->method('find')
            ->willReturn(

                (object)[

                    'code'=>'OLD',
                    'name'=>'OLD',
                    'description'=>'OLD'

                ]

            );


        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function($data){

                    return isset(
                        $data['updated_at']
                    );

                })
            )
            ->willReturn(true);



        $this->service->update(
            1,
            []
        );

    }





    // BUSINESS RULE: Delete creates audit log
    public function test_delete_lookup_group_creates_audit()
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