<?php


class RegisterValidator
{


    public function validate(array $data)
    {


        if (
            empty($data['name'])
        ) {
            throw new Exception(
                "Name required"
            );
        }



        if (
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new Exception(
                "Invalid email"
            );
        }




        if (
            strlen($data['password']) < 8
        ) {

            throw new Exception(
                "Password minimum 8 characters"
            );
        }



        if (
            $data['password']
            !=
            $data['password_confirm']
        ) {

            throw new Exception(
                "Password mismatch"
            );
        }



        return true;
    }
}
