<?php


use PHPUnit\Framework\TestCase;


class RoleServiceTest extends TestCase
{


    protected $repository;

    protected $service;



    protected function setUp(): void
    {

        $this->repository =
            $this->createMock(
                RoleRepositoryInterface::class
            );


        $this->service =
            new RoleService(
                $this->repository
            );

    }



    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_get_roles_success()
    {

        $roles = [

            (object)[
                'id'=>1,
                'name'=>'Admin'
            ]

        ];


        $this->repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($roles);



        $result =
            $this->service
                 ->getRoles();



        $this->assertEquals(
            $roles,
            $result
        );

    }





    public function test_get_role_success()
    {

        $role=(object)[

            'id'=>1,
            'name'=>'Admin'

        ];



        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($role);



        $this->assertEquals(
            $role,
            $this->service->getRole(1)
        );

    }





    public function test_create_role_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->with('Admin')
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['name']=='Admin'
                        &&
                        isset($data['created_at']);

                })
            )
            ->willReturn(1);



        $result =
            $this->service
                 ->create([
                    'name'=>'Admin'
                 ]);



        $this->assertEquals(
            1,
            $result
        );

    }





    public function test_update_role_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->with(
                'Manager',
                1
            )
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function($data){

                    return
                        $data['name']=='Manager'
                        &&
                        isset($data['updated_at']);

                })
            )
            ->willReturn(true);



        $result =
            $this->service
                 ->update(
                    1,
                    [
                        'name'=>'Manager'
                    ]
                 );



        $this->assertTrue(
            $result
        );

    }





    public function test_delete_role_success()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);



        $this->assertTrue(
            $this->service->delete(1)
        );

    }




    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */



    public function test_get_role_not_found()
    {

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $this->assertNull(
            $this->service->getRole(99)
        );

    }





    public function test_create_duplicate_role()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->with('Admin')
            ->willReturn(true);



        $this->expectException(
            Exception::class
        );


        $this->expectExceptionMessage(
            'Role already exists'
        );



        $this->service
             ->create([
                'name'=>'Admin'
             ]);

    }





    public function test_update_duplicate_role()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->with(
                'Admin',
                1
            )
            ->willReturn(true);



        $this->expectException(
            Exception::class
        );



        $this->service
             ->update(
                1,
                [
                    'name'=>'Admin'
                ]
             );

    }





    public function test_delete_role_failed()
    {

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(false);



        $this->assertFalse(
            $this->service->delete(1)
        );

    }





    public function test_repository_exception_get_roles()
    {

        $this->repository
            ->method('getAll')
            ->willThrowException(
                new Exception(
                    'Database error'
                )
            );



        $this->expectException(
            Exception::class
        );



        $this->service->getRoles();

    }





    public function test_repository_exception_create()
    {

        $this->repository
            ->method('existsName')
            ->willReturn(false);



        $this->repository
            ->method('create')
            ->willThrowException(
                new Exception(
                    'Database error'
                )
            );



        $this->expectException(
            Exception::class
        );



        $this->service
             ->create([
                'name'=>'Admin'
             ]);

    }





    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    public function test_create_empty_role_name()
    {

        $this->expectException(
            Exception::class
        );



        $this->service
             ->create([
                'name'=>''
             ]);

    }





    public function test_create_missing_name()
    {

        $this->expectException(
            Exception::class
        );



        $this->service
             ->create([]);

    }





    public function test_update_invalid_id()
    {

        $this->expectException(
            Exception::class
        );



        $this->service
             ->update(
                0,
                [
                    'name'=>'Admin'
                ]
             );

    }





    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */



    public function test_create_role_with_spaces()
    {

        $this->repository
            ->method('existsName')
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function($data){

                    return
                        $data['name']
                        ===
                        ' Admin ';

                })
            )
            ->willReturn(1);



        $this->service
             ->create([
                'name'=>' Admin '
             ]);

    }





    public function test_long_role_name()
    {

        $name =
            str_repeat(
                'A',
                255
            );



        $this->repository
            ->method('existsName')
            ->willReturn(false);



        $this->repository
            ->method('create')
            ->willReturn(1);



        $result =
            $this->service
                 ->create([
                    'name'=>$name
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
            ->method('existsName')
            ->with('Admin')
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturn(1);



        $this->service
             ->create([
                'name'=>'Admin'
             ]);

    }





    public function test_update_passes_role_id()
    {

        $this->repository
            ->expects($this->once())
            ->method('existsName')
            ->with(
                'Admin',
                10
            )
            ->willReturn(false);



        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->anything()
            )
            ->willReturn(true);



        $this->service
             ->update(
                10,
                [
                    'name'=>'Admin'
                ]
             );

    }





    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */


    public function test_role_name_must_be_unique()
    {

        $this->repository
            ->method('existsName')
            ->willReturn(true);



        $this->expectException(
            Exception::class
        );



        $this->service
             ->create([
                'name'=>'Admin'
             ]);

    }





    public function test_update_should_add_updated_timestamp()
    {

        $this->repository
            ->method('existsName')
            ->willReturn(false);



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



        $this->service
             ->update(
                1,
                [
                    'name'=>'Admin'
                ]
             );

    }


}