<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class PermissionRules
{


    public static function create()
    {

        return [

            [

                'field'=>'code',

                'label'=>'Permission Code',

                'rules'=>
                    'required|trim|max_length[100]'

            ],



            [

                'field'=>'name',

                'label'=>'Permission Name',

                'rules'=>
                    'required|trim|max_length[100]'

            ],



            [

                'field'=>'description',

                'label'=>'Description',

                'rules'=>
                    'trim|max_length[255]'

            ]

        ];

    }





    public static function update()
    {

        return [

            [

                'field'=>'code',

                'label'=>'Permission Code',

                'rules'=>
                    'required|trim|max_length[100]'

            ],



            [

                'field'=>'name',

                'label'=>'Permission Name',

                'rules'=>
                    'required|trim|max_length[100]'

            ],



            [

                'field'=>'description',

                'label'=>'Description',

                'rules'=>
                    'trim|max_length[255]'

            ]

        ];

    }





    public static function delete()
    {

        return [

            [

                'field'=>'id',

                'label'=>'Permission ID',

                'rules'=>
                    'required|integer'

            ]

        ];

    }


}