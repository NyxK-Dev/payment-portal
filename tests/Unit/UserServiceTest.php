<?php

use PHPUnit\Framework\TestCase;

require_once APPPATH . 'services/UserService.php';
require_once APPPATH . 'interfaces/UserRepositoryInterface.php';

class UserServiceTest extends TestCase
{


    protected $service;

    protected $userRepository;




    protected function setUp(): void
    {

        $this->userRepository =
            $this->createMock(
                UserRepositoryInterface::class
            );


        $this->service =
            new UserService(
                $this->userRepository
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */


    public function test_get_users_success()
    {

        $users = [

            [
                'id' => 1,
                'name' => 'Admin'
            ]

        ];



        $this->userRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($users);



        $result =
            $this->service
            ->getUsers();



        $this->assertEquals(
            $users,
            $result
        );
    }

    public function test_get_user_success()
    {

        $user = [

            'id' => 1,
            'name' => 'Admin'

        ];



        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($user);



        $result =
            $this->service
            ->getUser(1);



        $this->assertEquals(
            $user,
            $result
        );
    }

    public function test_get_role_by_name_success()
    {

        $role = [

            'id' => 1,
            'name' => 'admin'

        ];



        $this->userRepository
            ->expects($this->once())
            ->method('getRoleByName')
            ->with('admin')
            ->willReturn($role);



        $result =
            $this->service
            ->getRoleByName(
                'admin'
            );



        $this->assertEquals(
            $role,
            $result
        );
    }

    public function test_change_role_success()
    {

        $this->userRepository
            ->method('find')
            ->with(1)
            ->willReturn([

                'id' => 1,
                'role_id' => 3

            ]);



        $this->userRepository
            ->expects($this->once())
            ->method('updateRole')
            ->with(
                1,
                2
            )
            ->willReturn(true);



        $result =
            $this->service
            ->changeRole(
                1,
                2
            );



        $this->assertTrue(
            $result
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */



    public function test_get_user_not_found()
    {


        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn(null);



        $result =
            $this->service
            ->getUser(99);



        $this->assertNull(
            $result
        );
    }

    public function test_get_role_not_found()
    {


        $this->userRepository
            ->expects($this->once())
            ->method('getRoleByName')
            ->with('guest')
            ->willReturn(null);



        $result =
            $this->service
            ->getRoleByName(
                'guest'
            );



        $this->assertNull(
            $result
        );
    }

    public function test_change_role_failed()
    {

        $this->userRepository
            ->method('find')
            ->willReturn([

                'id' => 1,
                'role_id' => 3

            ]);



        $this->userRepository
            ->expects($this->once())
            ->method('updateRole')
            ->with(
                1,
                2
            )
            ->willReturn(false);



        $result =
            $this->service
            ->changeRole(
                1,
                2
            );



        $this->assertFalse(
            $result
        );
    }

    public function test_repository_exception_get_users()
    {


        $this->userRepository
            ->method('getAll')
            ->willThrowException(
                new Exception(
                    "Database error"
                )
            );



        $this->expectException(
            Exception::class
        );



        $this->service
            ->getUsers();
    }

    public function test_repository_exception_find_user()
    {


        $this->userRepository
            ->method('find')
            ->willThrowException(
                new Exception(
                    "Database error"
                )
            );



        $this->expectException(
            Exception::class
        );



        $this->service
            ->getUser(1);
    }
    public function test_repository_exception_role_lookup()
    {


        $this->userRepository
            ->method('getRoleByName')
            ->willThrowException(
                new Exception(
                    "Database error"
                )
            );



        $this->expectException(
            Exception::class
        );



        $this->service
            ->getRoleByName(
                'admin'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */


    public function test_get_user_invalid_zero_id()
    {


        $this->userRepository
            ->expects($this->never())
            ->method('find');



        $this->expectException(
            InvalidArgumentException::class
        );



        $this->service
            ->getUser(0);
    }

    public function test_get_user_negative_id()
    {


        $this->userRepository
            ->expects($this->never())
            ->method('find');



        $this->expectException(
            InvalidArgumentException::class
        );



        $this->service
            ->getUser(-1);
    }

    public function test_empty_role_name()
    {


        $this->userRepository
            ->expects($this->never())
            ->method('getRoleByName');



        $this->expectException(
            InvalidArgumentException::class
        );



        $this->service
            ->getRoleByName('');
    }







    public function test_invalid_change_role_user_id()
    {


        $this->userRepository
            ->expects($this->never())
            ->method('updateRole');



        $this->expectException(
            InvalidArgumentException::class
        );



        $this->service
            ->changeRole(
                0,
                2
            );
    }







    public function test_invalid_change_role_id()
    {


        $this->userRepository
            ->expects($this->never())
            ->method('updateRole');



        $this->expectException(
            InvalidArgumentException::class
        );



        $this->service
            ->changeRole(
                1,
                0
            );
    }








    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */


    public function test_empty_users_result()
    {


        $this->userRepository
            ->method('getAll')
            ->willReturn([]);



        $result =
            $this->service
            ->getUsers();



        $this->assertEmpty(
            $result
        );
    }







    public function test_null_users_result()
    {


        $this->userRepository
            ->method('getAll')
            ->willReturn(null);



        $result =
            $this->service
            ->getUsers();



        $this->assertNull(
            $result
        );
    }







    public function test_large_user_id()
    {


        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(
                PHP_INT_MAX
            )
            ->willReturn(null);



        $this->service
            ->getUser(
                PHP_INT_MAX
            );
    }







    public function test_numeric_string_user_id()
    {


        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with('1')
            ->willReturn([]);



        $this->service
            ->getUser(
                '1'
            );
    }







    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */


    public function test_get_user_calls_repository_once()
    {


        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(1);



        $this->service
            ->getUser(1);
    }







    public function test_role_lookup_calls_repository_once()
    {


        $this->userRepository
            ->expects($this->once())
            ->method('getRoleByName')
            ->with(
                'admin'
            );



        $this->service
            ->getRoleByName(
                'admin'
            );
    }
}
