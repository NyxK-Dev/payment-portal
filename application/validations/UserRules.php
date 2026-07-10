<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserRules
{
    public static function create()
    {
        return [

            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|trim|max_length[100]',
            ],

            [
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|trim|valid_email|max_length[255]',
            ],

            [
                'field' => 'role_id',
                'label' => 'Role',
                'rules' => 'required|integer',
            ],

            [
                'field' => 'status',
                'label' => 'Status',
                'rules' => 'required|integer',
            ],

        ];
    }

    public static function update()
    {
        return [

            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|trim|max_length[100]',
            ],

            [
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|trim|valid_email|max_length[255]',
            ],

            [
                'field' => 'role_id',
                'label' => 'Role',
                'rules' => 'required|integer',
            ],

            [
                'field' => 'status',
                'label' => 'Status',
                'rules' => 'required|integer',
            ],

        ];
    }

    public static function updateRole()
    {
        return [

            [
                'field' => 'role_id',
                'label' => 'Role',
                'rules' => 'required|integer',
            ],

        ];
    }

    public static function delete()
    {
        return [

            [
                'field' => 'id',
                'label' => 'User ID',
                'rules' => 'required|integer',
            ],

        ];
    }
}