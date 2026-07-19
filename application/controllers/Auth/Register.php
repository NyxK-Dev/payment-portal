<?php

defined('BASEPATH') or exit('No direct script access allowed');


// Service
require_once APPPATH . 'services/RegisterService.php';


// Interfaces
require_once APPPATH . 'interfaces/UserRepositoryInterface.php';
require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';


// Repositories
require_once APPPATH . 'repositories/UserRepository.php';
require_once APPPATH . 'repositories/RoleRepository.php';
require_once APPPATH . 'repositories/LookupRepository.php';


// Other services
require_once APPPATH . 'services/Auth_Service.php';
require_once APPPATH . 'services/Verification_service.php';
require_once APPPATH . 'services/EmailService.php';
require_once APPPATH . 'services/Recaptcha_service.php';


// Validator
require_once APPPATH . 'validators/RegisterValidator.php';



class Register extends MY_Controller
{


    protected $registerService;



    public function __construct()
    {
        parent::__construct();



        /*
        |--------------------------------------------------------------------------
        | Repositories
        |--------------------------------------------------------------------------
        */


        $userRepository =
            new UserRepository();



        $roleRepository =
            new RoleRepository();



        $lookupRepository =
            new LookupRepository();





        /*
        |--------------------------------------------------------------------------
        | Auth Service
        |--------------------------------------------------------------------------
        */


        $auth =
            new Auth();



        $authService =
            new Auth_service(

                $userRepository,

                $auth

            );






        /*
        |--------------------------------------------------------------------------
        | Other Services
        |--------------------------------------------------------------------------
        */


        $verificationService =
            new Verification_service();



        $emailService =
            new EmailService();



        $recaptchaService =
            new Recaptcha_service();




        /*
        |--------------------------------------------------------------------------
        | Validator
        |--------------------------------------------------------------------------
        */


        $validator =
            new RegisterValidator();







        /*
        |--------------------------------------------------------------------------
        | Register Service
        |--------------------------------------------------------------------------
        */


        $this->registerService =
            new RegisterService(

                $userRepository,

                $roleRepository,

                $lookupRepository,

                $authService,

                $verificationService,

                $emailService,

                $recaptchaService,

                $validator

            );

    }








    /**
     * Show register page
     */
    public function index()
    {


        if (
            $this->auth->check()
        ) {

            return $this->redirectByRole();

        }





        $this->render_auth(

            'auth/register',

            [

                'title' => 'Register'

            ]

        );

    }









    /**
     * Register submit
     */
    public function store()
    {


        try {


            $result =
                $this->registerService
                     ->register(


                        [

                            'name' =>

                                $this->input
                                     ->post(
                                        'name',
                                        TRUE
                                     ),



                            'email' =>

                                $this->input
                                     ->post(
                                        'email',
                                        TRUE
                                     ),



                            'password' =>

                                $this->input
                                     ->post(
                                        'password'
                                     ),



                            'password_confirm' =>

                                $this->input
                                     ->post(
                                        'password_confirm'
                                     ),



                            'captcha' =>

                                $this->input
                                     ->post(
                                        'g-recaptcha-response',
                                        TRUE
                                     ),



                            'ip' =>

                                $this->input
                                     ->ip_address()

                        ]

                    );







            $this->session
                 ->set_flashdata(

                    'info',

                    'A verification code has been sent to your email.'

                 );







            return redirect(

                'auth/verify/index/'
                .
                $result['user_id']

            );





        } catch (Exception $e) {



            $this->session
                 ->set_flashdata(

                    'error',

                    $e->getMessage()

                 );




            return redirect(
                'register'
            );

        }


    }









    /**
     * RBAC redirect
     */
    protected function redirectByRole()
    {


        if (
            $this->auth->isAdmin()
        ) {


            return redirect(

                'admin/users'

            );

        }





        return redirect(

            'user/products'

        );

    }



}