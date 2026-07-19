<?php

use PHPUnit\Framework\TestCase;


require_once APPPATH . 'services/RegisterService.php';
require_once APPPATH . 'validators/RegisterValidator.php';

require_once APPPATH . 'interfaces/UserRepositoryInterface.php';
require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

require_once APPPATH . 'repositories/LookupRepository.php';

require_once APPPATH . 'services/Auth_Service.php';
require_once APPPATH . 'services/Verification_service.php';
require_once APPPATH . 'services/EmailService.php';
require_once APPPATH . 'services/Recaptcha_service.php';



/*
|--------------------------------------------------------------------------
| Fake CodeIgniter get_instance()
|--------------------------------------------------------------------------
*/

if (!function_exists('get_instance')) {

    function &get_instance()
    {
        global $CI;

        return $CI;
    }
}





class RegisterServiceTest extends TestCase
{


    private $service;

    private $userRepository;

    private $roleRepository;

    private $lookupRepository;

    private $authService;

    private $verificationService;

    private $emailService;

    private $recaptchaService;

    private $validator;



    protected function setUp(): void
    {


        /*
        |--------------------------------------------------------------------------
        | Fake CI Instance
        |--------------------------------------------------------------------------
        */

        global $CI;


        $CI = new stdClass();



        $CI->load = new class {


            public function view(
                $view,
                $data = [],
                $return = false
            ) {

                return "
                    Verification Email

                    Code:
                    {$data['code']}

                    User:
                    {$data['user']->id}
                ";
            }
        };





        /*
        |--------------------------------------------------------------------------
        | Mock Dependencies
        |--------------------------------------------------------------------------
        */


        $this->userRepository =
            $this->createMock(
                UserRepositoryInterface::class
            );



        $this->roleRepository =
            $this->createMock(
                RoleRepositoryInterface::class
            );



        $this->lookupRepository =
            $this->createMock(
                LookupRepository::class
            );



        $this->authService =
            $this->createMock(
                Auth_Service::class
            );



        $this->verificationService =
            $this->createMock(
                Verification_service::class
            );



        $this->emailService =
            $this->createMock(
                EmailService::class
            );



        $this->recaptchaService =
            $this->createMock(
                Recaptcha_service::class
            );



        $this->validator =
            $this->createMock(
                RegisterValidator::class
            );





        $this->service =
            new RegisterService(

                $this->userRepository,

                $this->roleRepository,

                $this->lookupRepository,

                $this->authService,

                $this->verificationService,

                $this->emailService,

                $this->recaptchaService,

                $this->validator

            );
    }






    public function test_register_successfully_creates_user()
    {


        $this->prepareSuccess();



        $result =
            $this->service->register(
                $this->validData()
            );



        $this->assertEquals(

            [
                'user_id' => 1
            ],

            $result

        );
    }







    public function test_fail_when_captcha_invalid()
    {


        $this->recaptchaService
            ->method('isConfigured')
            ->willReturn(true);



        $this->recaptchaService
            ->method('verify')
            ->willReturn(false);



        $this->expectException(Exception::class);



        $this->service
            ->register(
                $this->validData()
            );
    }







    public function test_fail_when_role_missing()
    {


        $this->prepareBasic();



        $this->roleRepository
            ->method('findByName')
            ->willReturn(null);



        $this->expectException(Exception::class);



        $this->service
            ->register(
                $this->validData()
            );
    }








    public function test_fail_when_status_missing()
    {


        $this->prepareBasic();



        $this->roleRepository
            ->method('findByName')
            ->willReturn(
                (object)['id' => 1]
            );



        $this->lookupRepository
            ->method('findByCode')
            ->willReturn(null);



        $this->expectException(Exception::class);



        $this->service
            ->register(
                $this->validData()
            );
    }







    public function test_fail_when_user_creation_failed()
    {

        $this->prepareBasic();


        $this->roleRepository
            ->method('findByName')
            ->willReturn(
                (object)[
                    'id' => 1
                ]
            );


        $this->lookupRepository
            ->method('findByCode')
            ->willReturn(
                (object)[
                    'id' => 10
                ]
            );


        $this->authService
            ->method('hashPassword')
            ->willReturn(
                'hashed'
            );


        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn(0);



        $this->expectException(Exception::class);


        $this->service
            ->register(
                $this->validData()
            );
    }







    public function test_fail_when_email_failed()
    {


        $this->prepareBasic();



        $this->roleRepository
            ->method('findByName')
            ->willReturn(
                (object)[
                    'id' => 1
                ]
            );



        $this->lookupRepository
            ->method('findByCode')
            ->willReturn(
                (object)[
                    'id' => 10
                ]
            );



        $this->authService
            ->method('hashPassword')
            ->willReturn(
                'hashed'
            );



        $this->userRepository
            ->method('create')
            ->willReturn(1);



        $this->userRepository
            ->method('findById')
            ->willReturn(
                (object)[
                    'id' => 1,
                    'email' => 'test@test.com'
                ]
            );



        $this->verificationService
            ->method('generateCode')
            ->willReturn(
                '123456'
            );



        /*
    |--------------------------------------------------------------------------
    | Email failure
    |--------------------------------------------------------------------------
    */

        $this->emailService
            ->expects($this->once())
            ->method('sendVerification')
            ->willReturn(false);



        $this->expectException(Exception::class);



        $this->service
            ->register(
                $this->validData()
            );
    }


    public function test_fail_when_verification_code_generation_failed()
    {


        $this->prepareSuccess();



        $this->verificationService
            ->method('generateCode')
            ->willThrowException(
                new Exception(
                    "Verification failed"
                )
            );



        $this->expectException(Exception::class);



        $this->service
            ->register(
                $this->validData()
            );
    }


    public function test_email_trimmed()
    {


        $this->prepareSuccess();



        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->with(

                $this->callback(

                    function ($data) {

                        return
                            $data['email']
                            ===
                            'test@test.com';
                    }

                )

            )
            ->willReturn(1);



        $this->service
            ->register([

                'name' => 'John',

                'email' => '   TEST@TEST.COM   ',

                'password' => 'password123',

                'captcha' => 'captcha',

                'ip' => '127.0.0.1'

            ]);
    }

    public function test_empty_email_rejected()
    {


        $this->validator
            ->method('validate')
            ->willThrowException(
                new Exception()
            );


        $this->expectException(Exception::class);


        $this->service
            ->register([

                'email' => '',

            ]);
    }
    public function test_password_is_hashed()
    {


        $this->prepareSuccess();



        $this->authService
            ->expects($this->once())
            ->method('hashPassword')
            ->with(
                'password123'
            )
            ->willReturn(
                'hashed'
            );



        $this->service
            ->register(
                $this->validData()
            );
    }
    public function test_inactive_status_lookup_called()
    {


        $this->prepareSuccess();



        $this->lookupRepository
            ->expects($this->once())
            ->method('findByCode')
            ->with(
                'user_status',
                'inactive'
            );



        $this->service
            ->register(
                $this->validData()
            );
    }
    public function test_email_lowercase()
    {


        $this->prepareSuccess();



        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->with(

                $this->callback(

                    function ($data) {

                        return
                            $data['email']
                            ===
                            'test@test.com';
                    }

                )

            )
            ->willReturn(1);



        $this->service->register([

            'name' => 'John',

            'email' => 'TEST@TEST.COM',

            'password' => 'password',

            'captcha' => 'captcha',

            'ip' => '127.0.0.1'

        ]);
    }








    public function test_user_repository_payload()
    {


        $this->prepareSuccess();



        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->with(

                $this->callback(

                    function ($data) {

                        return

                            isset($data['role_id'])
                            &&
                            isset($data['status_lookup_id'])
                            &&
                            isset($data['password']);
                    }

                )

            )
            ->willReturn(1);



        $this->service
            ->register(
                $this->validData()
            );
    }







    public function test_new_user_has_customer_role()
    {


        $this->prepareSuccess();



        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->with(

                $this->callback(

                    function ($data) {

                        return
                            $data['role_id'] == 1;
                    }

                )

            )
            ->willReturn(1);



        $this->service
            ->register(
                $this->validData()
            );
    }








    public function test_new_user_is_inactive()
    {


        $this->prepareSuccess();



        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->with(

                $this->callback(

                    function ($data) {

                        return
                            $data['status_lookup_id'] == 10;
                    }

                )

            )
            ->willReturn(1);



        $this->service
            ->register(
                $this->validData()
            );
    }







    public function test_verification_email_sent()
    {


        $this->prepareSuccess();



        $this->emailService
            ->expects($this->once())
            ->method('sendVerification');



        $this->service
            ->register(
                $this->validData()
            );
    }








    private function prepareBasic()
    {

        $this->validator
            ->method('validate');


        $this->recaptchaService
            ->method('isConfigured')
            ->willReturn(false);
    }






    private function prepareSuccess()
    {


        $this->prepareBasic();



        $this->roleRepository
            ->method('findByName')
            ->willReturn(
                (object)[
                    'id' => 1
                ]
            );



        $this->lookupRepository
            ->method('findByCode')
            ->willReturn(
                (object)[
                    'id' => 10
                ]
            );



        $this->authService
            ->method('hashPassword')
            ->willReturn(
                'hashed'
            );



        $this->userRepository
            ->method('create')
            ->willReturn(1);



        $this->userRepository
            ->method('findById')
            ->willReturn(
                (object)[
                    'id' => 1
                ]
            );



        $this->verificationService
            ->method('generateCode')
            ->willReturn(
                '123456'
            );



        $this->emailService
            ->method('sendVerification')
            ->willReturn(true);
    }






    private function validData()
    {

        return [

            'name' => 'John',

            'email' => 'test@test.com',

            'password' => 'password123',

            'captcha' => 'captcha',

            'ip' => '127.0.0.1'

        ];
    }
}
