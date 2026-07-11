<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceRules
{
    public static function create()
    {
        return [
            [
                'field' => 'order_id',
                'label' => 'Order ID',
                'rules' => 'trim|required|integer'
            ],
            [
                'field' => 'invoice_no',
                'label' => 'Invoice Number',
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
        return [
            [
                'field' => 'status_lookup_id',
                'label' => 'Status Lookup ID',
                'rules' => 'trim|required|integer'
            ]
        ];
    }
}
