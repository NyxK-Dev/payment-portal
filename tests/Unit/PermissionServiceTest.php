<?php

use PHPUnit\Framework\TestCase;


class PermissionServiceTest extends TestCase
{

    protected $repository;

    protected $auditService;

    protected $service;



    protected function setUp(): void
    {

        $this->repository =
            $this->createMock(
                PermissionRepositoryInterface::class
            );


        $this->auditService =
            $this->createMock(
                AuditLogService::class
            );


        $this->service =
            new PermissionService(
                $this->repository,
                $this->auditService
            );
    }



    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */



    public function test_get_permissions_success()
    {

        $permissions = [

            (object)[
                'id' => 1,
                'code' => 'USER_CREATE'
            ]

        ];


        $this->repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($permissions);



        $result =
            $this->service
            ->getPermissions();



        $this->assertEquals(
            $permissions,
            $result
        );
    }




    public function test_get_permission_success()
    {

        $permission =
            (object)[
                'id' => 1,
                'code' => 'USER_CREATE'
            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($permission);



        $result =
            $this->service
            ->getPermission(1);



        $this->assertEquals(
            $permission,
            $result
        );
    }






    public function test_create_permission_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with(
                'USER_CREATE'
            )
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $this->auditService
            ->expects($this->once())
            ->method('log')
            ->willReturn(true);



        $result =
            $this->service
            ->create([

                'code' => 'USER_CREATE',
                'name' => 'Create User'

            ]);



        $this->assertEquals(
            1,
            $result
        );
    }





    public function test_update_permission_success()
    {

        $old =
            (object)[

                'id' => 1,
                'code' => 'OLD',
                'name' => 'Old',
                'description' => null

            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($old);



        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with(
                'NEW',
                1
            )
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);



        $result =
            $this->service
            ->update(
                1,
                [
                    'code' => 'NEW',
                    'name' => 'New'
                ]
            );



        $this->assertTrue(
            $result
        );
    }






    public function test_delete_permission_success()
    {

        $permission =
            (object)[

                'id' => 1,
                'code' => 'USER_CREATE'

            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($permission);



        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);



        $this->assertTrue(
            $this->service
                ->delete(1)
        );
    }






    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_failed()
    {

        $this->repository
            ->method('existsCode')
            ->willReturn(false);


        $this->repository
            ->method('create')
            ->willReturn(0);



        $result =
            $this->service
            ->create([

                'code' => 'TEST',
                'name' => 'Test'

            ]);



        $this->assertEquals(
            0,
            $result
        );
    }







    public function test_update_failed()
    {


        $permission =
            (object)[

                'id' => 1,
                'code' => 'OLD',
                'name' => 'Old',
                'description' => null

            ];



        $this->repository
            ->method('find')
            ->willReturn($permission);



        $this->repository
            ->method('existsCode')
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturn(false);



        $result =
            $this->service
            ->update(
                1,
                [
                    'name' => 'New'
                ]
            );



        $this->assertFalse(
            $result
        );
    }






    public function test_delete_failed()
    {

        $permission =
            (object)[
                'id' => 1
            ];



        $this->repository
            ->method('find')
            ->willReturn($permission);



        $this->repository
            ->method('delete')
            ->willReturn(false);



        $this->assertFalse(
            $this->service->delete(1)
        );
    }






    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_without_code()
    {


        $this->expectException(
            Exception::class
        );


        $this->expectExceptionMessage(
            'Permission code is required'
        );



        $this->service
            ->create([

                'name' => 'Test'

            ]);
    }






    public function test_create_without_name()
    {


        $this->expectException(
            Exception::class
        );


        $this->expectExceptionMessage(
            'Permission name is required'
        );



        $this->service
            ->create([

                'code' => 'TEST'

            ]);
    }







    public function test_duplicate_permission_code()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->willReturn(true);



        $this->expectException(
            Exception::class
        );



        $this->expectExceptionMessage(
            'Permission code already exists'
        );



        $this->service
            ->create([

                'code' => 'USER_CREATE',
                'name' => 'Create'

            ]);
    }






    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */



    public function test_empty_permissions()
    {

        $this->repository
            ->method('getAll')
            ->willReturn([]);



        $result =
            $this->service
            ->getPermissions();



        $this->assertEmpty(
            $result
        );
    }






    public function test_permission_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);



        $result =
            $this->service
            ->getPermission(999);



        $this->assertNull(
            $result
        );
    }






    public function test_delete_missing_permission()
    {

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->willReturn(null);



        $result =
            $this->service
            ->delete(99);



        $this->assertFalse(
            $result
        );
    }







    public function test_create_long_permission_code()
    {


        $code =
            str_repeat(
                'A',
                255
            );



        $this->repository
            ->method('existsCode')
            ->willReturn(false);



        $this->repository
            ->method('create')
            ->willReturn(1);



        $result =
            $this->service
            ->create([

                'code' => $code,
                'name' => 'Test'

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



    public function test_create_checks_duplicate_before_insert()
    {


        $this->repository
            ->expects($this->once())
            ->method('existsCode')
            ->with(
                'TEST'
            );



        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $this->service
            ->create([

                'code' => 'TEST',
                'name' => 'Test'

            ]);
    }







    public function test_update_finds_old_record_first()
    {

        $permission =
            (object)[
                'code' => 'OLD',
                'name' => 'Old',
                'description' => null
            ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($permission);



        $this->repository
            ->method('existsCode')
            ->willReturn(false);



        $this->repository
            ->method('update')
            ->willReturn(true);



        $this->service
            ->update(
                1,
                [
                    'name' => 'New'
                ]
            );
    }







    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */



    public function test_update_missing_permission_returns_false()
    {

        $this->repository
            ->method('find')
            ->willReturn(null);



        $result =
            $this->service
            ->update(
                100,
                [
                    'name' => 'Test'
                ]
            );



        $this->assertFalse(
            $result
        );
    }







    public function test_update_duplicate_code_is_blocked()
    {

        $permission =
            (object)[

                'code' => 'OLD',
                'name' => 'Old',
                'description' => null

            ];



        $this->repository
            ->method('find')
            ->willReturn($permission);



        $this->repository
            ->method('existsCode')
            ->willReturn(true);



        $this->expectException(
            Exception::class
        );



        $this->service
            ->update(
                1,
                [
                    'code' => 'NEW'
                ]
            );
    }







    public function test_failed_create_should_not_audit()
    {


        $this->repository
            ->method('existsCode')
            ->willReturn(false);



        $this->repository
            ->method('create')
            ->willReturn(0);



        $this->auditService
            ->expects($this->never())
            ->method('log');



        $result =
            $this->service
            ->create([

                'code' => 'TEST',
                'name' => 'Test'

            ]);



        $this->assertEquals(
            0,
            $result
        );
    }
}
