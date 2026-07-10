<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LookupRules
{
    public static function create()
    {
        return [
            [
                'field' => 'code',
                'label' => 'Code',
                'rules' => 'trim|required|max_length[100]'
            ],
            [
                'field' => 'value',
                'label' => 'Value',
                'rules' => 'trim|required|max_length[255]'
            ],
            [
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim'
            ],
            [
                'field' => 'sort_order',
                'label' => 'Sort Order',
                'rules' => 'integer'
            ],
            [
                'field' => 'is_active',
                'label' => 'Status',
                'rules' => 'required|in_list[0,1]'
            ]

        ];
    }

    public static function update()
    {
        return self::create();
    }

    public static function delete()
    {
        return [
            [
                'field' => 'id',
                'label' => 'Lookup ID',
                'rules' => 'required|integer'
            ]
        ];
    }
}
