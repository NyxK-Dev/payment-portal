<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginRules
{
    public static function authenticate()
    {
        return [
            [
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|trim|valid_email|max_length[255]'
            ],

            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[8]'
            ],

            [
                'field' => 'g-recaptcha-response',
                'label' => 'reCAPTCHA',
                'rules' => 'required'
            ]
        ];
    }
}