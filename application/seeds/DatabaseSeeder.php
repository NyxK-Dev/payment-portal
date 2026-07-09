<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DatabaseSeeder
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run()
    {
        $this->resetSeedData();

        require_once APPPATH . 'seeds/RoleSeeder.php';
        require_once APPPATH . 'seeds/LookupSeeder.php';
        require_once APPPATH . 'seeds/UserSeeder.php';
        require_once APPPATH . 'seeds/ProductSeeder.php';
        require_once APPPATH . 'seeds/OrderSeeder.php';
        require_once APPPATH . 'seeds/PaymentSeeder.php';

        (new RoleSeeder())->run();
        (new LookupSeeder())->run();
        (new UserSeeder())->run();
        (new ProductSeeder())->run();
        (new OrderSeeder())->run();
        (new PaymentSeeder())->run();
    }

    protected function resetSeedData()
    {
        $tables = [
            'role_permissions',
            'permissions',
            'lookups',
            'lookup_groups',
            'order_items',
            'payment_events',
            'stripe_transactions',
            'refunds',
            'receipts',
            'invoices',
            'payment_attempts',
            'payments',
            'orders',
            'products',
            'idempotency_keys',
            'api_tokens',
            'audit_logs',
            'activity_logs',
            'email_logs',
            'settings',
            'stripe_webhook_events',
            'users',
            'roles',
        ];

        $this->CI->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            if ($this->CI->db->table_exists($table)) {
                $this->CI->db->truncate($table);
            }
        }

        $this->CI->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}