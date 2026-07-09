<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LookupSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $groups = [
            ['code' => 'user_status', 'name' => 'User status', 'description' => 'Active and inactive user states'],
            ['code' => 'product_status', 'name' => 'Product status', 'description' => 'Active and inactive product states'],
            ['code' => 'order_status', 'name' => 'Order status', 'description' => 'Order lifecycle states'],
            ['code' => 'payment_status', 'name' => 'Payment status', 'description' => 'Payment lifecycle states'],
            ['code' => 'refund_status', 'name' => 'Refund status', 'description' => 'Refund lifecycle states'],
            ['code' => 'product_category', 'name' => 'Product category', 'description' => 'Catalog product categories'],
        ];

        $this->CI->db->insert_batch('lookup_groups', array_map(function ($group) use ($now) {
            return [
                'code' => $group['code'],
                'name' => $group['name'],
                'description' => $group['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $groups));

        $groupMap = [];
        foreach ($this->CI->db->get('lookup_groups')->result() as $group) {
            $groupMap[$group->code] = $group->id;
        }

        $lookups = [
            ['group_id' => $groupMap['user_status'], 'code' => 'active', 'value' => 'Active', 'description' => 'User account is active', 'sort_order' => 1, 'is_active' => 1],
            ['group_id' => $groupMap['user_status'], 'code' => 'inactive', 'value' => 'Inactive', 'description' => 'User account is inactive', 'sort_order' => 2, 'is_active' => 1],
            ['group_id' => $groupMap['product_status'], 'code' => 'active', 'value' => 'Active', 'description' => 'Product is available for sale', 'sort_order' => 1, 'is_active' => 1],
            ['group_id' => $groupMap['product_status'], 'code' => 'inactive', 'value' => 'Inactive', 'description' => 'Product is not available', 'sort_order' => 2, 'is_active' => 1],
            ['group_id' => $groupMap['order_status'], 'code' => 'pending', 'value' => 'Pending', 'description' => 'Order has not yet been paid', 'sort_order' => 1, 'is_active' => 1],
            ['group_id' => $groupMap['order_status'], 'code' => 'paid', 'value' => 'Paid', 'description' => 'Order payment completed', 'sort_order' => 2, 'is_active' => 1],
            ['group_id' => $groupMap['order_status'], 'code' => 'failed', 'value' => 'Failed', 'description' => 'Order payment failed', 'sort_order' => 3, 'is_active' => 1],
            ['group_id' => $groupMap['order_status'], 'code' => 'cancelled', 'value' => 'Cancelled', 'description' => 'Order was cancelled', 'sort_order' => 4, 'is_active' => 1],
            ['group_id' => $groupMap['order_status'], 'code' => 'refunded', 'value' => 'Refunded', 'description' => 'Order has been refunded', 'sort_order' => 5, 'is_active' => 1],
            ['group_id' => $groupMap['payment_status'], 'code' => 'pending', 'value' => 'Pending', 'description' => 'Payment attempt pending', 'sort_order' => 1, 'is_active' => 1],
            ['group_id' => $groupMap['payment_status'], 'code' => 'paid', 'value' => 'Paid', 'description' => 'Payment has been completed', 'sort_order' => 2, 'is_active' => 1],
            ['group_id' => $groupMap['payment_status'], 'code' => 'failed', 'value' => 'Failed', 'description' => 'Payment has failed', 'sort_order' => 3, 'is_active' => 1],
            ['group_id' => $groupMap['payment_status'], 'code' => 'refunded', 'value' => 'Refunded', 'description' => 'Payment was refunded', 'sort_order' => 4, 'is_active' => 1],
            ['group_id' => $groupMap['payment_status'], 'code' => 'partially_refunded', 'value' => 'Partially Refunded', 'description' => 'Payment partially refunded', 'sort_order' => 5, 'is_active' => 1],
            ['group_id' => $groupMap['refund_status'], 'code' => 'pending', 'value' => 'Pending', 'description' => 'Refund is pending', 'sort_order' => 1, 'is_active' => 1],
            ['group_id' => $groupMap['refund_status'], 'code' => 'succeeded', 'value' => 'Succeeded', 'description' => 'Refund completed successfully', 'sort_order' => 2, 'is_active' => 1],
            ['group_id' => $groupMap['refund_status'], 'code' => 'failed', 'value' => 'Failed', 'description' => 'Refund failed', 'sort_order' => 3, 'is_active' => 1],
            ['group_id' => $groupMap['refund_status'], 'code' => 'cancelled', 'value' => 'Cancelled', 'description' => 'Refund was cancelled', 'sort_order' => 4, 'is_active' => 1],
            ['group_id' => $groupMap['product_category'], 'code' => 'software', 'value' => 'Software', 'description' => 'Software and SaaS products', 'sort_order' => 1, 'is_active' => 1],
            ['group_id' => $groupMap['product_category'], 'code' => 'hardware', 'value' => 'Hardware', 'description' => 'Hardware and physical goods', 'sort_order' => 2, 'is_active' => 1],
        ];

        $this->CI->db->insert_batch('lookups', array_map(function ($lookup) use ($now) {
            return [
                'group_id' => $lookup['group_id'],
                'code' => $lookup['code'],
                'value' => $lookup['value'],
                'description' => $lookup['description'],
                'sort_order' => $lookup['sort_order'],
                'is_active' => $lookup['is_active'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $lookups));
    }
}
