<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Api_Controller.php';

class Orders extends MY_Api_Controller
{
    public function __construct()
    {
        parent::__construct();

        // JWT authentication is required for all endpoints in this controller
        $this->requireAuth();

        // Load the order service layer
        $this->load->service('OrderService');
    }

    /**
     * GET /api/v1/orders
     * Customer: View own order history
     */
    public function index()
    {
        // echo json_encode($this->authUser);
        // exit;
        $this->only(['GET']);

        $filters = [
            'keyword' => $this->input->get('keyword'),
            'from'    => $this->input->get('from'),
            'to'      => $this->input->get('to')
        ];

        $orders = $this->orderservice->getOrderHistory(
            $this->authUser->id,
            $filters
        );

        $this->sendResponse($orders, 'Orders retrieved successfully');
    }

    /**
     * GET /api/v1/orders/{id}
     * Customer: Own order only
     * Admin: Any order
     */
    public function show($id)
    {
        $this->only(['GET']);

        $order = $this->orderservice->getOrderDetail($id);

        if (!$order) {
            $this->sendError('Order not found', [], 404);
        }

        // Ownership Check: Customers are restricted to their own orders
        if ($this->authUser->role !== 'admin' && $order->user_id != $this->authUser->id) {
            $this->sendError('You cannot access this order', [], 403);
        }

        $this->sendResponse($order, 'Order retrieved successfully');
    }

    /**
     * POST /api/v1/orders
     * Customer creates order
     */
    public function store()
    {
        $this->only(['POST']);

        $input = $this->getJsonInput();

        $this->validateRequest('Order', 'create', $input);

        $order = $this->orderservice->createOrder(
            $this->authUser->id,
            $input['items']
        );

        $this->sendResponse($order, 'Order created successfully', 201);
    }

    /**
     * GET /api/v1/orders/admin
     * Admin: View all orders
     */
    public function adminIndex()
    {
        $this->only(['GET']);
        $this->requirePermission('manage_orders');

        $orders = $this->orderservice->getAllOrders();

        $this->sendResponse($orders, 'All orders retrieved successfully');
    }

    /**
     * PATCH /api/v1/orders/{id}/status
     * Admin update order status
     */
    public function updateStatus($id)
    {
        $this->only(['PATCH', 'PUT']);
        $this->requirePermission('manage_orders');

        $input = $this->getJsonInput();

        $this->validateRequest('Order', 'updateStatus', $input);

        $updated = $this->orderservice->updateStatus(
            $id,
            $input['status_lookup_id']
        );

        if (!$updated) {
            $this->sendError('Order status update failed', [], 400);
        }

        $order = $this->orderservice->getOrderDetail($id);

        $this->sendResponse($order, 'Order status updated successfully');
    }
}
