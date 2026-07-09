<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class RoleRules
{

    /**
     * Create Role
     */
    public static function create()
    {
        return [

            [
                'field' => 'name',
                'label' => 'Role Name',
                'rules' => 'required|trim|max_length[100]'
            ],

            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|max_length[255]'
            ]

        ];
    }





    /**
     * Update Role
     */
    public static function update()
    {
        return [

            [
                'field' => 'name',
                'label' => 'Role Name',
                'rules' => 'required|trim|max_length[100]'
            ],

            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|max_length[255]'
            ]

        ];
    }





    /**
     * Delete Role
     */
    public static function delete()
    {
        return [

            [
                'field' => 'id',
                'label' => 'Role ID',
                'rules' => 'required|integer'
            ]

        ];
    }


}