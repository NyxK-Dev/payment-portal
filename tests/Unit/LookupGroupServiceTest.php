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





    public function test_create_lookup_group()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['code'] === 'PAYMENT' &&
                        $data['name'] === 'Payment' &&
                        isset($data['created_at']);

                })
            )
            ->willReturn(10);



        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->with(
                'CREATE',
                'LOOKUP_GROUP',
                10,
                null,
                $this->isType('array')
            );



        $result =
            $this->service
                ->create([
                    'code'=>'PAYMENT',
                    'name'=>'Payment',
                    'description'=>'Payment status'
                ]);



        $this->assertEquals(
            10,
            $result
        );
    }






    public function test_create_lookup_group_failed()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(0);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $result =
            $this->service
                ->create([
                    'code'=>'TEST',
                    'name'=>'Test'
                ]);



        $this->assertEquals(
            0,
            $result
        );
    }






    public function test_update_lookup_group()
    {

        $oldRecord = (object)[

            'code'=>'OLD',
            'name'=>'Old Name',
            'description'=>'Old Description'

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
                        $data['code'] === 'NEW' &&
                        $data['name'] === 'New Name' &&
                        isset($data['updated_at']);

                })
            )
            ->willReturn(true);




        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->with(
                'UPDATE',
                'LOOKUP_GROUP',
                1,
                $this->isType('array'),
                $this->isType('array')
            );



        $result =
            $this->service
                ->update(
                    1,
                    [
                        'code'=>'NEW',
                        'name'=>'New Name'
                    ]
                );



        $this->assertTrue(
            $result
        );
    }







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



        $result =
            $this->service
                ->update(
                    99,
                    [
                        'name'=>'Test'
                    ]
                );



        $this->assertFalse(
            $result
        );
    }







    public function test_update_lookup_group_failed()
    {

        $oldRecord = (object)[

            'code'=>'OLD',
            'name'=>'Old',
            'description'=>'Old'

        ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->willReturn($oldRecord);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $result =
            $this->service
                ->update(
                    1,
                    [
                        'name'=>'New'
                    ]
                );



        $this->assertFalse(
            $result
        );
    }







    public function test_delete_lookup_group()
    {

        $oldRecord = (object)[

            'id'=>1,
            'name'=>'Payment'

        ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($oldRecord);



        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->with(
                'DELETE',
                'LOOKUP_GROUP',
                1,
                $this->isType('array'),
                null
            );



        $result =
            $this->service
                ->delete(1);



        $this->assertTrue(
            $result
        );
    }







    public function test_delete_lookup_group_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $this->repository
            ->expects($this->never())
            ->method('delete');



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $result =
            $this->service
                ->delete(99);



        $this->assertFalse(
            $result
        );
    }







    public function test_get_all_lookup_groups()
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



        $result =
            $this->service
                ->getAll();



        $this->assertEquals(
            $groups,
            $result
        );
    }







    public function test_find_lookup_group()
    {

        $group = (object)[
            'id'=>1
        ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($group);



        $result =
            $this->service
                ->find(1);



        $this->assertEquals(
            $group,
            $result
        );
    }
    

}