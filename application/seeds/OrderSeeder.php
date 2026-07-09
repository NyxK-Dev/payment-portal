<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $customer = $this->CI->db->get_where('users', ['email' => 'customer@example.com'])->row();
        $paidStatus = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'order_status')
            ->where('lookups.code', 'paid')
            ->get()
            ->row();
        $starterPlan = $this->CI->db->get_where('products', ['sku' => 'SP-001'])->row();
        $reader = $this->CI->db->get_where('products', ['sku' => 'HW-001'])->row();

        if (!$customer || !$paidStatus || !$starterPlan || !$reader) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $orderId = $this->CI->db->insert('orders', [
            'user_id' => $customer->id,
            'order_no' => 'ORD-20260709-0001',
            'status_lookup_id' => $paidStatus->id,
            'total_amount' => 179.98,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->CI->db->insert_batch('order_items', [
            [
                'order_id' => $orderId,
                'product_id' => $starterPlan->id,
                'quantity' => 1,
                'unit_price' => 49.99,
                'subtotal' => 49.99,
                'created_at' => $now,
            ],
            [
                'order_id' => $orderId,
                'product_id' => $reader->id,
                'quantity' => 1,
                'unit_price' => 129.99,
                'subtotal' => 129.99,
                'created_at' => $now,
            ],
        ]);
    }
}
