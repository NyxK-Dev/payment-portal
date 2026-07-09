<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property CI_Session $session
 */
class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        // Fetch real count statistics
        $stats = [
            'users' => $this->db->count_all('users'),
            'products' => $this->db->count_all('products'),
            'orders' => $this->db->count_all('orders'),
            'payments' => $this->db->count_all('payments'),
            'refunds' => $this->db->count_all('refunds'),
        ];

        // Fetch recent orders contextual data to eliminate empty spaces
        $recent_orders = $this->db->select('orders.*, users.name as customer_name')
            ->from('orders')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->order_by('orders.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->result_array();

        $data = [
            'title' => 'System Dashboard',
            'stats' => $stats,
            'recent_orders' => $recent_orders
        ];

        $this->render('admin/dashboard/index', $data);
    }
}
