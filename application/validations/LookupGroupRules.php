<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class LookupGroupRules
{

    /**
     * Create lookup group
     */
    public static function create()
    {
        return [

            [
                'field' => 'code',
                'label' => 'Code',
                'rules' => 'required|max_length[100]'
            ],

            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'trim|max_length[255]'
            ],

            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim'
            ]

        ];
    }


    /**
     * Update lookup group
     */
    public static function update()
    {
        return [

            [
                'field' => 'code',
                'label' => 'Code',
                'rules' => 'required|max_length[100]'
            ],

            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'trim|max_length[255]'
            ],

            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim'
            ]

        ];
    }


    /**
     * Delete lookup group
     */
    public static function delete()
    {
        return [

            [
                'field'=>'id',
                'label'=>'Lookup Group ID',
                'rules'=>'required|integer'
            ]

        ];
    }

}