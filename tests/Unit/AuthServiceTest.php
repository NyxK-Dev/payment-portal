<?php

use PHPUnit\Framework\TestCase;


class AuthServiceTest extends TestCase
{

    protected $service;
    protected $userRepository;
    protected $auth;
    protected $statusQuery;



    protected function setUp(): void
    {

        /*
    |--------------------------------------------------------------------------
    | Mock Query Result
    |--------------------------------------------------------------------------
    */

        $this->statusQuery =
            $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'row'
            ])
            ->getMock();



        // Default status = active
        $this->statusQuery
            ->method('row')
            ->willReturn(
                (object)[
                    'code' => 'active'
                ]
            );



        /*
    |--------------------------------------------------------------------------
    | Mock Database
    |--------------------------------------------------------------------------
    */


        $db =
            $this->getMockBuilder(stdClass::class)
            ->addMethods([
                'select',
                'from',
                'join',
                'where',
                'get'
            ])
            ->getMock();



        /*
    |--------------------------------------------------------------------------
    | Query Builder Chain
    |--------------------------------------------------------------------------
    */


        $db->method('select')
            ->willReturn($db);


        $db->method('from')
            ->willReturn($db);


        $db->method('join')
            ->willReturn($db);


        $db->method('where')
            ->willReturn($db);



        $db->method('get')
            ->willReturn(
                $this->statusQuery
            );



        /*
    |--------------------------------------------------------------------------
    | Fake CodeIgniter Instance
    |--------------------------------------------------------------------------
    */


        global $CI;


        $CI = new stdClass();


        $CI->db = $db;


        $GLOBALS['CI'] = $CI;



        /*
    |--------------------------------------------------------------------------
    | Repository Mock
    |--------------------------------------------------------------------------
    */


        $this->userRepository =
            $this->createMock(
                UserRepositoryInterface::class
            );



        /*
    |--------------------------------------------------------------------------
    | Auth Mock
    |--------------------------------------------------------------------------
    */


        $this->auth =
            $this->createMock(
                Auth::class
            );



        /*
    |--------------------------------------------------------------------------
    | Create Service
    |--------------------------------------------------------------------------
    */


        $this->service =
            new Auth_service(
                $this->userRepository,
                $this->auth
            );
    }


    private function createActiveUser()
    {

        return (object)[

            'id' => 1,

            'email' => 'test@test.com',

            'password' => password_hash(
                '123456',
                PASSWORD_DEFAULT
            ),

            'deleted_at' => null,

            'status_lookup_id' => 1

        ];
    }




    /*
    |--------------------------------------------------------------------------
    | SUCCESS CASES
    |--------------------------------------------------------------------------
    */



    public function test_successful_login()
    {

        $user = $this->createActiveUser();



        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@test.com')
            ->willReturn($user);



        $this->userRepository
            ->expects($this->once())
            ->method('updateLastLogin')
            ->with(1);



        $this->auth
            ->expects($this->once())
            ->method('login')
            ->with($user);



        $result =
            $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );



        $this->assertTrue(
            $result['success']
        );


        $this->assertEquals(
            $user,
            $result['user']
        );
    }





    public function test_password_hash_success()
    {

        $hash =
            $this->service
            ->hashPassword(
                '123456'
            );


        $this->assertTrue(
            password_verify(
                '123456',
                $hash
            )
        );
    }





    public function test_password_verify_success()
    {

        $hash =
            password_hash(
                '123456',
                PASSWORD_DEFAULT
            );


        $this->assertTrue(

            $this->service
                ->verifyPassword(
                    '123456',
                    $hash
                )

        );
    }




    public function test_logout_success()
    {

        $this->auth
            ->expects($this->once())
            ->method('logout');



        $this->service
            ->logout();
    }







    /*
    |--------------------------------------------------------------------------
    | FAILURE CASES
    |--------------------------------------------------------------------------
    */





    public function test_user_not_found()
    {

        $this->userRepository
            ->method('findByEmail')
            ->willReturn(null);



        $result =
            $this->service
            ->attempt(
                'abc@test.com',
                '123456'
            );



        $this->assertFalse(
            $result['success']
        );


        $this->assertEquals(
            'Invalid email or password.',
            $result['message']
        );
    }





    public function test_wrong_password()
    {

        $user = $this->createActiveUser();


        $this->userRepository
            ->method('findByEmail')
            ->willReturn($user);



        $this->auth
            ->expects($this->never())
            ->method('login');



        $this->userRepository
            ->expects($this->never())
            ->method('updateLastLogin');



        $result =
            $this->service
            ->attempt(
                'test@test.com',
                'wrong'
            );


        $this->assertFalse(
            $result['success']
        );
    }





    public function test_deleted_account()
    {

        $user = $this->createActiveUser();


        $user->deleted_at = '2026-01-01';



        $this->userRepository
            ->method('findByEmail')
            ->willReturn($user);



        $result =
            $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );



        $this->assertFalse(
            $result['success']
        );


        $this->assertEquals(
            'This account has been deleted.',
            $result['message']
        );
    }






   public function test_attempt_inactive_account()
{

    /*
    |--------------------------------------------------------------------------
    | Mock inactive status lookup
    |--------------------------------------------------------------------------
    */

    $query = $this->getMockBuilder(stdClass::class)
        ->addMethods([
            'row'
        ])
        ->getMock();


    $query->method('row')
        ->willReturn(
            (object)[
                'code' => 'inactive'
            ]
        );



    $db = $this->getMockBuilder(stdClass::class)
        ->addMethods([
            'select',
            'from',
            'join',
            'where',
            'get'
        ])
        ->getMock();



    $db->method('select')
        ->willReturn($db);


    $db->method('from')
        ->willReturn($db);


    $db->method('join')
        ->willReturn($db);


    $db->method('where')
        ->willReturn($db);


    $db->method('get')
        ->willReturn($query);



    global $CI;


    $CI = new stdClass();

    $CI->db = $db;



    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */


    $user = (object)[

        'id'=>1,

        'password'=>password_hash(
            '123456',
            PASSWORD_DEFAULT
        ),

        'deleted_at'=>null,

        'status_lookup_id'=>2

    ];



    $this->userRepository
        ->expects($this->once())
        ->method('findByEmail')
        ->with('admin@test.com')
        ->willReturn($user);



    /*
    |--------------------------------------------------------------------------
    | Should stop here
    |--------------------------------------------------------------------------
    */


    $this->userRepository
        ->expects($this->never())
        ->method('updateLastLogin');


    $this->auth
        ->expects($this->never())
        ->method('login');



    $result =
        $this->service
            ->attempt(
                'admin@test.com',
                '123456'
            );



    $this->assertFalse(
        $result['success']
    );


    $this->assertEquals(
        'Your account is inactive.',
        $result['message']
    );

}







    /*
    |--------------------------------------------------------------------------
    | VALIDATION CASES
    |--------------------------------------------------------------------------
    */





    public function test_empty_email()
    {

        $this->userRepository
            ->expects($this->never())
            ->method('findByEmail');



        $result =
            $this->service
            ->attempt(
                '',
                '123456'
            );



        $this->assertFalse(
            $result['success']
        );
    }





    public function test_empty_password()
    {

        $result =
            $this->service
            ->attempt(
                'test@test.com',
                ''
            );


        $this->assertFalse(
            $result['success']
        );
    }





    public function test_invalid_email_format()
    {

        $result =
            $this->service
            ->attempt(
                'wrong-email',
                '123456'
            );


        $this->assertFalse(
            $result['success']
        );
    }








    /*
    |--------------------------------------------------------------------------
    | EDGE CASES
    |--------------------------------------------------------------------------
    */





    public function test_user_without_deleted_at_field()
    {

        $user = (object)[

            'id' => 1,

            'password' => password_hash(
                '123456',
                PASSWORD_DEFAULT
            ),

            'status_lookup_id' => 1

        ];



        $this->userRepository
            ->method('findByEmail')
            ->willReturn($user);



        $result =
            $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );


        $this->assertTrue(
            $result['success']
        );
    }






    public function test_empty_password_hash()
    {

        $user = $this->createActiveUser();


        $user->password = '';



        $this->userRepository
            ->method('findByEmail')
            ->willReturn($user);



        $result =
            $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );



        $this->assertFalse(
            $result['success']
        );
    }








    /*
    |--------------------------------------------------------------------------
    | REPOSITORY INTERACTION RULES
    |--------------------------------------------------------------------------
    */





    public function test_failed_login_should_not_update_last_login()
    {

        $this->userRepository
            ->method('findByEmail')
            ->willReturn(null);



        $this->userRepository
            ->expects($this->never())
            ->method('updateLastLogin');



        $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );
    }






    public function test_failed_login_should_not_call_auth_login()
    {

        $this->userRepository
            ->method('findByEmail')
            ->willReturn(null);



        $this->auth
            ->expects($this->never())
            ->method('login');



        $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );
    }






    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC RULES
    |--------------------------------------------------------------------------
    */





    public function test_active_user_updates_login_before_authentication()
    {

        $user = $this->createActiveUser();



        $sequence =
            $this->userRepository
            ->expects($this->once())
            ->method('updateLastLogin')
            ->with(1);



        $this->userRepository
            ->method('findByEmail')
            ->willReturn($user);



        $this->auth
            ->expects($this->once())
            ->method('login')
            ->with($user);



        $result =
            $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );


        $this->assertTrue(
            $result['success']
        );
    }





    public function test_repository_exception_is_not_hidden()
    {

        $this->userRepository
            ->method('findByEmail')
            ->willThrowException(
                new Exception('Database error')
            );


        $this->expectException(Exception::class);



        $this->service
            ->attempt(
                'test@test.com',
                '123456'
            );
    }
}
