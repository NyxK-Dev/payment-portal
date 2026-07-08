<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_all_tables extends CI_Migration
{
    private function get_columns($table)
    {
        $columns = $this->db->query('SHOW COLUMNS FROM `' . $table . '`')->result_array();
        return array_column($columns, 'Field');
    }

    private function filter_data($table, array $data)
    {
        $columns = $this->get_columns($table);
        $allowed = array_fill_keys($columns, true);
        return array_intersect_key($data, $allowed);
    }

    private function insert_if_missing($table, array $data, array $where = array())
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($this->db->from($table)->count_all_results() > 0) {
            return false;
        }

        $filtered = $this->filter_data($table, $data);
        if (!empty($filtered)) {
            $this->db->insert($table, $filtered);
        }

        return true;
    }

    private function insert_unique($table, array $data, array $where)
    {
        $this->db->from($table);
        foreach ($where as $field => $value) {
            $this->db->where($field, $value);
        }

        if ($this->db->count_all_results() > 0) {
            return false;
        }

        $filtered = $this->filter_data($table, $data);
        if (!empty($filtered)) {
            $this->db->insert($table, $filtered);
        }

        return true;
    }

    private function get_row($table, array $where)
    {
        $this->db->where($where);
        return $this->db->get($table)->row();
    }

    private function ensure_role($name, $description)
    {
        $row = $this->get_row('roles', array('name' => $name));
        if ($row) {
            return $row->id;
        }

        $this->db->insert('roles', array(
            'name'        => $name,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ));

        return $this->db->insert_id();
    }

    private function ensure_permission($code, $name, $description)
    {
        $row = $this->get_row('permissions', array('code' => $code));
        if ($row) {
            return $row->id;
        }

        $this->db->insert('permissions', array(
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ));

        return $this->db->insert_id();
    }

    private function ensure_lookup_group($code, $name, $description)
    {
        $row = $this->get_row('lookup_groups', array('code' => $code));
        if ($row) {
            return $row->id;
        }

        $this->db->insert('lookup_groups', array(
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s'),
        ));

        return $this->db->insert_id();
    }

    private function ensure_lookup($group_code, $code, $value, $description, $sort_order = 0)
    {
        $group = $this->get_row('lookup_groups', array('code' => $group_code));
        if (!$group) {
            return null;
        }

        $row = $this->get_row('lookups', array('group_id' => $group->id, 'code' => $code));
        if ($row) {
            return $row->id;
        }

        $this->db->insert('lookups', array(
            'group_id'    => $group->id,
            'code'        => $code,
            'value'       => $value,
            'description' => $description,
            'sort_order'  => $sort_order,
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
        ));

        return $this->db->insert_id();
    }

    private function get_lookup_id($group_code, $code)
    {
        $group = $this->get_row('lookup_groups', array('code' => $group_code));
        if (!$group) {
            return null;
        }

        $row = $this->get_row('lookups', array('group_id' => $group->id, 'code' => $code));
        return $row ? $row->id : null;
    }

    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $admin_role_id = $this->ensure_role('admin', 'Administrator with full access to the portal');
        $customer_role_id = $this->ensure_role('customer', 'Standard customer');

        $user_status_group = $this->ensure_lookup_group('user_status', 'User Status', 'Lifecycle status for portal users');
        $product_status_group = $this->ensure_lookup_group('product_status', 'Product Status', 'Lifecycle status for catalog products');
        $order_status_group = $this->ensure_lookup_group('order_status', 'Order Status', 'Status values for orders');
        $payment_status_group = $this->ensure_lookup_group('payment_status', 'Payment Status', 'Status values for payments');
        $invoice_status_group = $this->ensure_lookup_group('invoice_status', 'Invoice Status', 'Status values for invoices');
        $receipt_status_group = $this->ensure_lookup_group('receipt_status', 'Receipt Status', 'Status values for receipts');

        $this->ensure_lookup('user_status', 'active', 'Active', 'Active user', 1);
        $this->ensure_lookup('user_status', 'inactive', 'Inactive', 'Inactive user', 2);
        $this->ensure_lookup('product_status', 'active', 'Active', 'Active product', 1);
        $this->ensure_lookup('product_status', 'inactive', 'Inactive', 'Inactive product', 2);
        $this->ensure_lookup('order_status', 'pending', 'Pending', 'Order is being prepared', 1);
        $this->ensure_lookup('order_status', 'paid', 'Paid', 'Order is paid', 2);
        $this->ensure_lookup('order_status', 'cancelled', 'Cancelled', 'Order was cancelled', 3);
        $this->ensure_lookup('payment_status', 'pending', 'Pending', 'Payment pending', 1);
        $this->ensure_lookup('payment_status', 'paid', 'Paid', 'Payment complete', 2);
        $this->ensure_lookup('payment_status', 'refunded', 'Refunded', 'Payment refunded', 3);
        $this->ensure_lookup('invoice_status', 'issued', 'Issued', 'Invoice issued', 1);
        $this->ensure_lookup('invoice_status', 'paid', 'Paid', 'Invoice paid', 2);
        $this->ensure_lookup('receipt_status', 'issued', 'Issued', 'Receipt issued', 1);
        $this->ensure_lookup('receipt_status', 'void', 'Void', 'Receipt voided', 2);

        $user_status_id = $this->get_lookup_id('user_status', 'active');
        $product_status_id = $this->get_lookup_id('product_status', 'active');
        $order_status_id = $this->get_lookup_id('order_status', 'paid');
        $payment_status_id = $this->get_lookup_id('payment_status', 'paid');
        $invoice_status_id = $this->get_lookup_id('invoice_status', 'issued');
        $receipt_status_id = $this->get_lookup_id('receipt_status', 'issued');

        $admin_user = $this->get_row('users', array('email' => 'admin@example.com'));
        if (!$admin_user) {
            $this->db->insert('users', $this->filter_data('users', array(
                'role_id'         => $admin_role_id,
                'name'            => 'Admin User',
                'email'           => 'admin@example.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'status_lookup_id'=> $user_status_id,
                'created_at'      => $now,
                'updated_at'      => $now,
            )));
            $admin_user = $this->get_row('users', array('email' => 'admin@example.com'));
        }

        $customer_user = $this->get_row('users', array('email' => 'customer@example.com'));
        if (!$customer_user) {
            $this->db->insert('users', $this->filter_data('users', array(
                'role_id'         => $customer_role_id,
                'name'            => 'Customer User',
                'email'           => 'customer@example.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'status_lookup_id'=> $user_status_id,
                'created_at'      => $now,
                'updated_at'      => $now,
            )));
            $customer_user = $this->get_row('users', array('email' => 'customer@example.com'));
        }

        $manage_products = $this->ensure_permission('manage_products', 'Manage products', 'Create, edit and remove products');
        $manage_orders = $this->ensure_permission('manage_orders', 'Manage orders', 'Manage customer orders');
        $manage_payments = $this->ensure_permission('manage_payments', 'Manage payments', 'Handle payment workflows');
        $manage_users = $this->ensure_permission('manage_users', 'Manage users', 'Manage portal users');
        $view_reports = $this->ensure_permission('view_reports', 'View reports', 'Access dashboard reporting');

        $this->insert_unique('role_permissions', array(
            'role_id'        => $admin_role_id,
            'permission_id'  => $manage_products,
            'created_at'     => $now,
        ), array('role_id' => $admin_role_id, 'permission_id' => $manage_products));
        $this->insert_unique('role_permissions', array(
            'role_id'        => $admin_role_id,
            'permission_id'  => $manage_orders,
            'created_at'     => $now,
        ), array('role_id' => $admin_role_id, 'permission_id' => $manage_orders));
        $this->insert_unique('role_permissions', array(
            'role_id'        => $admin_role_id,
            'permission_id'  => $manage_payments,
            'created_at'     => $now,
        ), array('role_id' => $admin_role_id, 'permission_id' => $manage_payments));
        $this->insert_unique('role_permissions', array(
            'role_id'        => $admin_role_id,
            'permission_id'  => $manage_users,
            'created_at'     => $now,
        ), array('role_id' => $admin_role_id, 'permission_id' => $manage_users));
        $this->insert_unique('role_permissions', array(
            'role_id'        => $admin_role_id,
            'permission_id'  => $view_reports,
            'created_at'     => $now,
        ), array('role_id' => $admin_role_id, 'permission_id' => $view_reports));

        $this->insert_unique('role_permissions', array(
            'role_id'        => $customer_role_id,
            'permission_id'  => $manage_orders,
            'created_at'     => $now,
        ), array('role_id' => $customer_role_id, 'permission_id' => $manage_orders));

        $this->insert_unique('settings', array(
            'setting_key'    => 'site_name',
            'setting_value'  => 'Payment Portal',
            'description'    => 'Application display name',
            'updated_at'     => $now,
        ), array('setting_key' => 'site_name'));
        $this->insert_unique('settings', array(
            'setting_key'    => 'default_currency',
            'setting_value'  => 'USD',
            'description'    => 'Default currency for invoices and receipts',
            'updated_at'     => $now,
        ), array('setting_key' => 'default_currency'));
        $this->insert_unique('settings', array(
            'setting_key'    => 'stripe_enabled',
            'setting_value'  => '1',
            'description'    => 'Enable Stripe checkout',
            'updated_at'     => $now,
        ), array('setting_key' => 'stripe_enabled'));

        $this->insert_unique('products', array(
            'sku'               => 'SKU-1001',
            'name'              => 'Wireless Mouse',
            'description'       => 'Ergonomic wireless mouse',
            'price'             => '29.99',
            'status'            => 'active',
            'status_lookup_id'  => $product_status_id,
            'category_lookup_id'=> null,
            'created_by'        => $admin_user ? $admin_user->id : null,
            'created_at'        => $now,
            'updated_at'        => $now,
        ), array('sku' => 'SKU-1001'));
        $this->insert_unique('products', array(
            'sku'               => 'SKU-1002',
            'name'              => 'Mechanical Keyboard',
            'description'       => 'Compact mechanical keyboard',
            'price'             => '89.99',
            'status'            => 'active',
            'status_lookup_id'  => $product_status_id,
            'category_lookup_id'=> null,
            'created_by'        => $admin_user ? $admin_user->id : null,
            'created_at'        => $now,
            'updated_at'        => $now,
        ), array('sku' => 'SKU-1002'));

        $product_1 = $this->get_row('products', array('sku' => 'SKU-1001'));
        $product_2 = $this->get_row('products', array('sku' => 'SKU-1002'));

        $order_no = 'ORD-1001';
        $order = $this->get_row('orders', array('order_no' => $order_no));
        if (!$order) {
            $this->db->insert('orders', $this->filter_data('orders', array(
                'user_id'          => $customer_user ? $customer_user->id : $admin_user->id,
                'order_no'         => $order_no,
                'status_lookup_id' => $order_status_id,
                'total_amount'     => '129.98',
                'version'          => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            )));
            $order = $this->get_row('orders', array('order_no' => $order_no));
        }

        if ($product_1 && $order) {
            $this->insert_unique('order_items', array(
                'order_id'    => $order->id,
                'product_id'  => $product_1->id,
                'quantity'    => 1,
                'unit_price'  => '29.99',
                'subtotal'    => '29.99',
                'created_at'  => $now,
            ), array('order_id' => $order->id, 'product_id' => $product_1->id));
        }

        if ($product_2 && $order) {
            $this->insert_unique('order_items', array(
                'order_id'    => $order->id,
                'product_id'  => $product_2->id,
                'quantity'    => 1,
                'unit_price'  => '89.99',
                'subtotal'    => '89.99',
                'created_at'  => $now,
            ), array('order_id' => $order->id, 'product_id' => $product_2->id));
        }

        $payment = $this->get_row('payments', array('payment_no' => 'PAY-1001'));
        if (!$payment) {
            $this->db->insert('payments', $this->filter_data('payments', array(
                'order_id'         => $order ? $order->id : null,
                'payment_no'       => 'PAY-1001',
                'payment_method'   => 'card',
                'amount'           => '129.98',
                'currency'         => 'USD',
                'status_lookup_id' => $payment_status_id,
                'version'          => 1,
                'paid_at'          => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            )));
            $payment = $this->get_row('payments', array('payment_no' => 'PAY-1001'));
        }

        if ($payment) {
            $this->insert_unique('stripe_transactions', array(
                'payment_id'        => $payment->id,
                'provider'          => 'stripe',
                'stripe_session_id' => 'cs_test_1001',
                'payment_intent_id' => 'pi_test_1001',
                'currency'          => 'USD',
                'amount'            => '129.98',
                'provider_status'   => 'succeeded',
                'raw_payload'       => '{"status":"succeeded"}',
                'created_at'        => $now,
                'updated_at'        => $now,
            ), array('payment_id' => $payment->id));

            $invoice = $this->get_row('invoices', array('invoice_no' => 'INV-1001'));
            if (!$invoice) {
                $this->db->insert('invoices', $this->filter_data('invoices', array(
                    'order_id'         => $order ? $order->id : null,
                    'invoice_no'       => 'INV-1001',
                    'amount'           => '129.98',
                    'status_lookup_id' => $invoice_status_id,
                    'issued_at'        => $now,
                    'issued_by'        => $customer_user ? $customer_user->id : null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                )));
                $invoice = $this->get_row('invoices', array('invoice_no' => 'INV-1001'));
            }

            if ($invoice) {
                $this->insert_unique('invoice_line_items', array(
                    'invoice_id' => $invoice->id,
                    'product_name' => 'Wireless Mouse',
                    'unit_price' => '29.99',
                    'quantity' => 1,
                    'subtotal' => '29.99',
                    'created_at' => $now,
                ), array('invoice_id' => $invoice->id, 'product_name' => 'Wireless Mouse'));
            }

            $receipt = $this->get_row('receipts', array('receipt_no' => 'RCPT-1001'));
            if (!$receipt) {
                $this->db->insert('receipts', $this->filter_data('receipts', array(
                    'invoice_id'       => $invoice ? $invoice->id : null,
                    'receipt_no'       => 'RCPT-1001',
                    'amount'           => '129.98',
                    'status_lookup_id' => $receipt_status_id,
                    'issued_at'        => $now,
                    'issued_by'        => $customer_user ? $customer_user->id : null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                )));
            }

            $this->insert_unique('refunds', array(
                'payment_id'       => $payment->id,
                'refund_no'        => 'RF-1001',
                'stripe_refund_id' => 're_test_1001',
                'amount'           => '10.00',
                'reason'           => 'Customer requested reversal',
                'status_lookup_id' => null,
                'created_by'       => $customer_user ? $customer_user->id : null,
                'approved_by'      => $admin_user ? $admin_user->id : null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ), array('refund_no' => 'RF-1001'));
        }

        $this->insert_unique('api_tokens', array(
            'user_id'     => $admin_user ? $admin_user->id : null,
            'token_hash'  => hash('sha256', 'seed-admin-token'),
            'expires_at'  => date('Y-m-d H:i:s', strtotime('+30 days')),
            'last_used_at'=> $now,
            'created_at'  => $now,
        ), array('token_hash' => hash('sha256', 'seed-admin-token')));
        $this->insert_unique('api_tokens', array(
            'user_id'     => $customer_user ? $customer_user->id : null,
            'token_hash'  => hash('sha256', 'seed-customer-token'),
            'expires_at'  => date('Y-m-d H:i:s', strtotime('+30 days')),
            'last_used_at'=> $now,
            'created_at'  => $now,
        ), array('token_hash' => hash('sha256', 'seed-customer-token')));

        $this->insert_unique('audit_logs', array(
            'user_id'      => $admin_user ? $admin_user->id : null,
            'action'       => 'seed',
            'entity_type'  => 'users',
            'entity_id'    => $admin_user ? $admin_user->id : null,
            'old_data'     => null,
            'new_data'     => '{"seeded":true}',
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'Seeder',
            'created_at'   => $now,
        ), array('user_id' => ($admin_user ? $admin_user->id : null), 'action' => 'seed'));

        $this->insert_unique('activity_logs', array(
            'user_id'       => $customer_user ? $customer_user->id : null,
            'activity_type' => 'checkout',
            'description'   => 'Seeded checkout activity',
            'ip_address'    => '127.0.0.1',
            'created_at'    => $now,
        ), array('activity_type' => 'checkout', 'description' => 'Seeded checkout activity'));

        $this->insert_unique('email_logs', array(
            'user_id'   => $customer_user ? $customer_user->id : null,
            'email_to'  => 'customer@example.com',
            'subject'   => 'Welcome to Payment Portal',
            'status'    => 'sent',
            'response'  => 'Queued successfully',
            'sent_at'   => $now,
        ), array('email_to' => 'customer@example.com', 'subject' => 'Welcome to Payment Portal'));

        $this->insert_unique('stripe_webhook_events', array(
            'event_id'     => 'wh_1001',
            'event_type'   => 'checkout.session.completed',
            'processed'    => 1,
            'payload'      => '{"event":"checkout.session.completed"}',
            'processed_at' => $now,
            'created_at'   => $now,
        ), array('event_id' => 'wh_1001'));
    }

    public function down()
    {
        $this->db->where_in('email', array('admin@example.com', 'customer@example.com'));
        $this->db->delete('users');

        $this->db->where_in('code', array('manage_products', 'manage_orders', 'manage_payments', 'manage_users', 'view_reports'));
        $this->db->delete('permissions');

        $this->db->where_in('setting_key', array('site_name', 'default_currency', 'stripe_enabled'));
        $this->db->delete('settings');

        $this->db->where_in('sku', array('SKU-1001', 'SKU-1002'));
        $this->db->delete('products');

        $this->db->where_in('order_no', array('ORD-1001'));
        $this->db->delete('orders');

        $this->db->where_in('payment_no', array('PAY-1001'));
        $this->db->delete('payments');

        $this->db->where_in('invoice_no', array('INV-1001'));
        $this->db->delete('invoices');

        $this->db->where_in('receipt_no', array('RCPT-1001'));
        $this->db->delete('receipts');

        $this->db->where_in('stripe_refund_id', array('re_test_1001'));
        $this->db->delete('refunds');

        $this->db->where_in('event_id', array('wh_1001'));
        $this->db->delete('stripe_webhook_events');
    }
}
