<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RoleSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $this->CI->db->insert_batch('roles', [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access to the portal',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'customer',
                'description' => 'Standard customer who can purchase products',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $this->CI->db->insert_batch('permissions', [
            [
                'code' => 'manage_users',
                'name' => 'Manage users',
                'description' => 'Create, update, and delete user accounts',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'manage_products',
                'name' => 'Manage products',
                'description' => 'Create, update, and delete products',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'manage_orders',
                'name' => 'Manage orders',
                'description' => 'View and update customer orders and order status',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'manage_payments',
                'name' => 'Manage payments',
                'description' => 'Process payments and refunds',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'view_reports',
                'name' => 'View reports',
                'description' => 'Access sales and activity reports',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $adminRole = $this->CI->db->get_where('roles', ['name' => 'admin'])->row();
        $permissionIds = $this->CI->db->select('id')->from('permissions')->where_in('code', ['manage_users', 'manage_products', 'manage_orders', 'manage_payments', 'view_reports'])->get()->result_array();

        $rows = [];
        foreach ($permissionIds as $permission) {
            $rows[] = [
                'role_id' => $adminRole->id,
                'permission_id' => $permission['id'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        if ($rows) {
            $this->CI->db->insert_batch('role_permissions', $rows);
        }
    }
}