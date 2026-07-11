<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReceiptRules
{
    public static function create()
    {
        return [
            [
                'field' => 'invoice_id',
                'label' => 'Invoice ID',
                'rules' => 'trim|required|integer'
            ],
            [
                'field' => 'receipt_no',
                'label' => 'Receipt Number',
                'rules' => 'trim|required|max_length[100]'
            ],
            [
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'trim|required|numeric'
            ],
            [
                'field' => 'status_lookup_id',
                'label' => 'Status Lookup ID',
                'rules' => 'trim|integer'
            ]
        ];
    }

    public static function update()
    {
        return self::create();
    }
}
