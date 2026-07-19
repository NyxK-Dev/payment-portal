<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EmailService 
{


    protected $email;



    public function __construct()
    {

        $CI =& get_instance();

        $CI->load->library('email');

        $this->email = $CI->email;

    }





    /**
     * Send verification email
     */
    public function sendVerification(
        $user,
        string $body
    )
    {


        $from =
            getenv('SUPPORT_EMAIL')
            ?: 'support@example.com';



        $appName =
            getenv('APP_NAME')
            ?: 'Payment Portal';



        $this->email->clear();



        $this->email->from(
            $from,
            $appName
        );



        $this->email->to(
            $user->email
        );



        $this->email->subject(
            'Verify your email address'
        );



        $this->email->message(
            $body
        );



        $this->email->set_mailtype(
            'html'
        );



        $sent =
            $this->email->send();



        $this->logResult(
            $sent
        );



        return $sent;

    }






    /**
     * General HTML Email
     */
    public function sendHtmlEmail(
        string $to,
        string $subject,
        string $body
    )
    {


        $from =
            getenv('SUPPORT_EMAIL')
            ?: 'support@example.com';



        $appName =
            getenv('APP_NAME')
            ?: 'Payment Portal';




        $this->email->clear();




        $this->email->from(
            $from,
            $appName
        );



        $this->email->to(
            $to
        );



        $this->email->subject(
            $subject
        );



        $this->email->message(
            $body
        );



        $this->email->set_mailtype(
            'html'
        );




        $sent =
            $this->email->send();



        $this->logResult(
            $sent
        );



        return $sent;


    }






    /**
     * Development logging
     */
    private function logResult($sent)
    {


        if(
            getenv('APP_ENV') !== 'production'
        )
        {


            if(
                function_exists('log_message')
            )
            {

                log_message(
                    'debug',
                    'Email send result: '
                    .
                    var_export(
                        $sent,
                        true
                    )
                );



                log_message(
                    'debug',
                    'Email debugger: '
                    .
                    $this->email
                         ->print_debugger(
                            [
                                'headers'
                            ]
                         )
                );


            }

        }


    }


}