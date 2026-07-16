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



    public function test_create_lookup()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['group_id'] === 1 &&
                        $data['code'] === 'ACTIVE' &&
                        $data['value'] === 'Active' &&
                        $data['sort_order'] === 1 &&
                        $data['is_active'] === 1;
                })
            )
            ->willReturn(10);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $result =
            $this->service
            ->create([
                'group_id' => 1,
                'code' => 'ACTIVE',
                'value' => 'Active',
                'sort_order' => 1,
                'is_active' => 1
            ]);


        $this->assertEquals(
            10,
            $result
        );
    }




    public function test_create_lookup_failed()
    {

        $this->repository
            ->method('create')
            ->willReturn(0);


        $this->auditService
            ->expects($this->never())
            ->method('log');


        $result =
            $this->service
            ->create([]);


        $this->assertEquals(
            0,
            $result
        );
    }




    public function test_create_lookup_default_values()
    {

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {

                    return
                        $data['group_id'] === null &&
                        $data['sort_order'] === 0 &&
                        $data['is_active'] === 1;
                })
            )
            ->willReturn(1);


        $this->service
            ->create([]);
    }






    public function test_update_lookup()
    {

        $old =
            (object)[
                'code' => 'OLD',
                'value' => 'Old value',
                'description' => 'old',
                'sort_order' => 1,
                'is_active' => 1
            ];



        $this->repository
            ->method('find')
            ->willReturn($old);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);



        $this->auditService
            ->expects($this->once())
            ->method('log');



        $result =
            $this->service
            ->update(
                1,
                [
                    'value' => 'New'
                ]
            );


        $this->assertTrue($result);
    }





    public function test_update_lookup_not_found()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $this->repository
            ->expects($this->never())
            ->method('update');



        $result =
            $this->service
            ->update(
                99,
                []
            );


        $this->assertFalse($result);
    }






    public function test_update_lookup_failed()
    {

        $old =
            (object)[
                'code' => 'A',
                'value' => 'B',
                'description' => 'C',
                'sort_order' => 1,
                'is_active' => 1
            ];


        $this->repository
            ->method('find')
            ->willReturn($old);



        $this->repository
            ->method('update')
            ->willReturn(false);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $result =
            $this->service
            ->update(
                1,
                []
            );


        $this->assertFalse($result);
    }






    public function test_delete_lookup()
    {

        $old =
            (object)[
                'id' => 1
            ];


        $this->repository
            ->method('find')
            ->willReturn($old);



        $this->repository
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






    public function test_delete_lookup_not_found()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $this->repository
            ->expects($this->never())
            ->method('delete');


        $this->assertFalse(
            $this->service
                ->delete(99)
        );
    }





    public function test_delete_lookup_failed()
    {

        $this->repository
            ->method('find')
            ->willReturn(
                (object)['id' => 1]
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







    public function test_get_by_group()
    {

        $data = [
            (object)['id' => 1]
        ];


        $this->repository
            ->expects($this->once())
            ->method('getByGroup')
            ->with(5)
            ->willReturn($data);



        $this->assertEquals(
            $data,
            $this->service
                ->getByGroup(5)
        );
    }







    public function test_get_all_with_group()
    {

        $data = [
            (object)['id' => 1]
        ];


        $this->repository
            ->method('getAllWithGroup')
            ->willReturn($data);



        $this->assertEquals(
            $data,
            $this->service
                ->getAllWithGroup()
        );
    }







    public function test_find_lookup()
    {

        $lookup =
            (object)['id' => 1];


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






    public function test_find_lookup_not_found()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $this->assertNull(
            $this->service
                ->find(99)
        );
    }






    public function test_count_by_group()
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







    public function test_get_by_group_code()
    {

        $data = [
            (object)['code' => 'ACTIVE']
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
    public function test_get_by_group_code_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('getByGroupCode')
            ->with('UNKNOWN')
            ->willReturn(null);



        $result =
            $this->service
            ->getByGroupCode('UNKNOWN');



        $this->assertNull(
            $result
        );
    }
    public function test_get_by_group_failed()
    {

        $this->repository
            ->expects($this->once())
            ->method('getByGroup')
            ->with(99)
            ->willReturn([]);



        $result =
            $this->service
            ->getByGroup(99);



        $this->assertEmpty(
            $result
        );
    }
}
