<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $adminUser = $this->CI->db->get_where('users', ['email' => 'admin@example.com'])->row();
        $softwareCategory = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'product_category')
            ->where('lookups.code', 'software')
            ->get()
            ->row();
        $hardwareCategory = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'product_category')
            ->where('lookups.code', 'hardware')
            ->get()
            ->row();
        $activeStatus = $this->CI->db
            ->select('lookups.id')
            ->from('lookups')
            ->join('lookup_groups', 'lookup_groups.id = lookups.group_id')
            ->where('lookup_groups.code', 'product_status')
            ->where('lookups.code', 'active')
            ->get()
            ->row();

        $now = date('Y-m-d H:i:s');

        $this->CI->db->insert_batch('products', [
            [
                'category_lookup_id' => $softwareCategory ? $softwareCategory->id : null,
                'status_lookup_id' => $activeStatus ? $activeStatus->id : null,
                'name' => 'Starter plan',
                'description' => 'Monthly subscription for a single user.',
                'sku' => 'SP-001',
                'price' => 49.99,
                'stock_qty' => 100,
                'created_by' => $adminUser ? $adminUser->id : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_lookup_id' => $hardwareCategory ? $hardwareCategory->id : null,
                'status_lookup_id' => $activeStatus ? $activeStatus->id : null,
                'name' => 'Payment reader',
                'description' => 'Portable payment device for in-person payments.',
                'sku' => 'HW-001',
                'price' => 129.99,
                'stock_qty' => 25,
                'created_by' => $adminUser ? $adminUser->id : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
