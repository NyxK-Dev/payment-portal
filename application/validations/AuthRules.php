<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuthRules
{
    public static function register()
    {
        return [

            RuleBuilder::make(
                'name',
                'Name',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    RuleBuilder::min(3),
                    RuleBuilder::max(100)
                )
            ),

            RuleBuilder::make(
                'email',
                'Email',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    'valid_email',
                    RuleBuilder::max(150)
                )
            ),

            RuleBuilder::make(
                'password',
                'Password',
                RuleBuilder::password(8)
            )

        ];
    }

    public static function login()
    {
        return [

            RuleBuilder::make(
                'email',
                'Email',
                RuleBuilder::combine(
                    RuleBuilder::required(),
                    'valid_email'
                )
            ),

            RuleBuilder::make(
                'password',
                'Password',
                RuleBuilder::required()
            )

        ];
    }
}
