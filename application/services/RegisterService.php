<?php

defined('BASEPATH') OR exit('No direct script access allowed');


require_once APPPATH . 'interfaces/UserRepositoryInterface.php';
require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

require_once APPPATH . 'services/Auth_Service.php';
require_once APPPATH . 'services/Verification_service.php';
require_once APPPATH . 'services/EmailService.php';
require_once APPPATH . 'services/Recaptcha_service.php';

require_once APPPATH . 'validators/RegisterValidator.php';


class RegisterService
{


    protected $userRepository;

    protected $roleRepository;

    protected $lookupRepository;

    protected $authService;

    protected $verificationService;

    protected $emailService;

    protected $recaptchaService;

    protected $validator;




    public function __construct(

        UserRepositoryInterface $userRepository,

        RoleRepositoryInterface $roleRepository,

        LookupRepository $lookupRepository,

        Auth_Service $authService,

        Verification_service $verificationService,

        EmailService $emailService,

        Recaptcha_service $recaptchaService,

        RegisterValidator $validator

    )
    {


        $this->userRepository =
            $userRepository;


        $this->roleRepository =
            $roleRepository;


        $this->lookupRepository =
            $lookupRepository;


        $this->authService =
            $authService;


        $this->verificationService =
            $verificationService;


        $this->emailService =
            $emailService;


        $this->recaptchaService =
            $recaptchaService;


        $this->validator =
            $validator;


    }







    public function register(array $data)
    {


        /*
        |--------------------------------------------------------------------------
        | 1. Validate
        |--------------------------------------------------------------------------
        */


        $this->validator
             ->validate($data);





        /*
        |--------------------------------------------------------------------------
        | 2. Verify Captcha
        |--------------------------------------------------------------------------
        */


        if(
            $this->recaptchaService->isConfigured()
        )
        {

            $valid =
            $this->recaptchaService
                 ->verify(
                    $data['captcha'],
                    $data['ip']
                 );


            if(!$valid)
            {

                throw new Exception(
                    "reCAPTCHA verification failed."
                );

            }

        }






        /*
        |--------------------------------------------------------------------------
        | 3. Find Customer Role
        |--------------------------------------------------------------------------
        */


        $role =
        $this->roleRepository
             ->findByName(
                'customer'
             );


        if(!$role)
        {

            throw new Exception(
                "Customer role missing."
            );

        }







        /*
        |--------------------------------------------------------------------------
        | 4. Find Inactive Status
        |--------------------------------------------------------------------------
        */


$inactiveStatus =
$this->lookupRepository
     ->findByCode(
        'user_status',
        'inactive'
     );


if (!$inactiveStatus) {

    throw new Exception(
        'Inactive user status not found'
    );

}


if (!$inactiveStatus) {

    throw new Exception(
        'Inactive user status not found'
    );

}









        /*
        |--------------------------------------------------------------------------
        | 5. Create User
        |--------------------------------------------------------------------------
        */


        $userId =
        $this->userRepository
             ->create([


                'role_id' =>
                    $role->id,


                'name' =>
                    $data['name'],


                'email' =>
                    strtolower(
                        trim(
                            $data['email']
                        )
                    ),


                'password' =>
                    $this->authService
                         ->hashPassword(
                            $data['password']
                         ),


                'status_lookup_id' =>
                    $inactiveStatus->id,


                     'created_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),


                'updated_at' =>
                    date(
                        'Y-m-d H:i:s'
                    )


             ]);






        if(!$userId)
        {

            throw new Exception(
                "User creation failed."
            );

        }







        /*
        |--------------------------------------------------------------------------
        | 6. Get User
        |--------------------------------------------------------------------------
        */


        $user =
        $this->userRepository
             ->findById(
                $userId
             );



        if(!$user)
        {

            throw new Exception(
                "User not found after creation."
            );

        }







        /*
        |--------------------------------------------------------------------------
        | 7. Generate Verification Code
        |--------------------------------------------------------------------------
        */


        $code =
        $this->verificationService
             ->generateCode(

                $userId,

                getenv('VERIF_TTL_MINUTES')
                ?
                (int)getenv('VERIF_TTL_MINUTES')
                :
                60

             );








        /*
        |--------------------------------------------------------------------------
        | 8. Create Email Body
        |--------------------------------------------------------------------------
        */


        $CI =& get_instance();


        $body =
        $CI->load
            ->view(

                'emails/verification',

                [

                    'code'=>$code,

                    'user'=>$user

                ],

                true

            );








        /*
        |--------------------------------------------------------------------------
        | 9. Send Email
        |--------------------------------------------------------------------------
        */


        $sent =
        $this->emailService
             ->sendVerification(

                $user,

                $body

             );



        if(!$sent)
        {

            throw new Exception(
                "Unable to send verification email."
            );

        }







        return [

            'user_id'=>$userId

        ];


    }


}