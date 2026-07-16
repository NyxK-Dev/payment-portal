<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrderService
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        // Load required repositories
        $this->CI->load->repository('OrderRepository');
        $this->CI->load->repository('OrderItemRepository');
    }

    /**
     * Admin: Get all orders
     */
    public function getAllOrders()
    {
        return $this->CI->orderrepository->getAll();
    }

    /**
     * Create a new customer order and its nested items.
     * Shared by Web Checkout and API Order Create.
     */
    public function createOrder($userId, array $cart)
    {
        // 1. Business Validation
        if (!$this->validateItems($cart)) {
            throw new Exception('Invalid order items');
        }

        // 2. Calculate Total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // 3. Generate Order Number
        $orderNo = 'ORD-' . date('YmdHis');

        // 4. Create Order
        $orderId = $this->CI->orderrepository->create([
            'user_id'          => $userId,
            'order_no'         => $orderNo,
            'status_lookup_id' => 5, // Pending Status
            'total_amount'     => $total,
            'version'          => 1,
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        // 5. Build and Create Order Items
        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'order_id'   => $orderId,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal'   => $item['price'] * $item['quantity'],
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->CI->orderitemrepository->createBatch($items);

        return [
            'id'       => $orderId,
            'order_no' => $orderNo,
            'total'    => $total
        ];
    }

    /**
     * Validate Order Items structure and payload data.
     */
    protected function validateItems(array $cart)
    {
        if (empty($cart)) {
            return false;
        }

        foreach ($cart as $item) {
            // Check keys exist
            if (!isset($item['product_id']) || !isset($item['price']) || !isset($item['quantity'])) {
                return false;
            }

            // Check types are numeric
            if (!is_numeric($item['product_id']) || !is_numeric($item['price']) || !is_numeric($item['quantity'])) {
                return false;
            }

            // Check quantity boundaries
            if ($item['quantity'] <= 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve Order History for a specific user, with optional filters.
     */
    public function getOrderHistory($userId, $filters = [])
    {
        $orders = $this->CI->orderrepository->getByUser($userId, $filters);

        foreach ($orders as $order) {
            $order->items = $this->CI->orderitemrepository->getByOrderId($order->id);
        }

        return $orders;
    }

    /**
     * Retrieve a detailed single order view with its items.
     */
    public function getOrderDetail($id)
    {
        $order = $this->CI->orderrepository->findWithItems($id);

        if ($order) {
            $order->items = $this->CI->orderitemrepository->getByOrderId($id);
        }

        return $order;
    }

    /**
     * Update an existing order's status.
     */
    public function updateStatus($id, $statusId)
    {
        return $this->CI->orderrepository->update($id, [
            'status_lookup_id' => $statusId
        ]);
    }
}
