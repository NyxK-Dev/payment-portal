<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RolePermissionRules
{
    public static function create()
    {
        return [

            [
                'field' => 'role_id',
                'label' => 'Role',
                'rules' => 'required|integer'
            ],

            [
                'field' => 'permission_id[]',
                'label' => 'Permissions',
                'rules' => 'required'
            ]

        ];
    }

    public static function update()
    {
        return [

            [
                'field' => 'permission_id[]',
                'label' => 'Permissions',
                'rules' => 'required'
            ]

        ];
    }

    public static function delete()
    {
        return [

            [
                'field' => 'role_id',
                'label' => 'Role',
                'rules' => 'required|integer'
            ]

        ];
    }
}