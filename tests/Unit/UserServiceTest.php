<?php

use PHPUnit\Framework\TestCase;


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



    public function test_get_users()
    {

        $users=[
            [
                'id'=>1,
                'name'=>'Admin'
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



    public function test_get_users_empty()
    {

        $this->userRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);



        $result =
            $this->service
                ->getUsers();



        $this->assertEmpty(
            $result
        );

    }

    public function test_get_user()
    {

        $user=[
            'id'=>1,
            'name'=>'Admin'
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


    public function test_get_role_by_name()
    {

        $role=[
            'id'=>1,
            'name'=>'admin'
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

    public function test_get_role_by_name_not_found()
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

    public function test_change_role()
    {

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

    public function test_change_role_failed()
    {

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

}