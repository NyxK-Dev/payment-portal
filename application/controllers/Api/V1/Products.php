<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Api_Controller.php';

class Products extends MY_Api_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();

        $this->load->service('Product_Service');
        $this->load->repository('ProductRepository');
    }

    /**
     * GET /api/v1/products
     * List all products with optional keyword filtering.
     */
    public function index()
    {
        $this->only(['GET']);

        $keyword = $this->input->get('keyword');

        $products = $this->productrepository->findAll([
            'keyword' => $keyword
        ]);

        $this->sendResponse($products, 'Products retrieved successfully');
    }

    /**
     * GET /api/v1/products/{id}
     * Show a single product by its ID.
     */
    public function show($id)
    {
        $this->only(['GET']);

        $product = $this->productrepository->find($id);

        if (!$product) {
            $this->sendError('Product not found', [], 404);
        }

        $this->sendResponse($product, 'Product retrieved successfully');
    }

    /**
     * POST /api/v1/products
     * Create a new product (Requires manage_products permission).
     */
    public function store()
    {
        $this->only(['POST']);
        $this->requirePermission('manage_products');

        $input = $this->getJsonInput();

        $this->validateRequest('Product', 'create', $input);

        $productId = $this->product_service->create($input, $this->authUser->id);

        $product = $this->productrepository->find($productId);

        $this->sendResponse($product, 'Product created successfully', 201);
    }

    /**
     * PUT/PATCH /api/v1/products/{id}
     * Update an existing product (Requires manage_products permission).
     */
    public function update($id)
    {
        $this->only(['PUT', 'PATCH']);
        $this->requirePermission('manage_products');

        $product = $this->productrepository->find($id);

        if (!$product) {
            $this->sendError('Product not found', [], 404);
        }

        $input = $this->getJsonInput();

        $this->validateRequest('Product', 'update', $input);

        $result = $this->product_service->update($id, $input);

        if (!$result) {
            $this->sendError('Product update failed', [], 400);
        }

        $updatedProduct = $this->productrepository->find($id);

        $this->sendResponse($updatedProduct, 'Product updated successfully');
    }

    /**
     * DELETE /api/v1/products/{id}
     * Delete a product (Requires manage_products permission).
     */
    public function delete($id)
    {
        $this->only(['DELETE']);
        $this->requirePermission('manage_products');

        $product = $this->productrepository->find($id);

        if (!$product) {
            $this->sendError('Product not found', [], 404);
        }

        $result = $this->product_service->delete($id);

        if (!$result) {
            $this->sendError('Product delete failed', [], 400);
        }

        $this->sendResponse([], 'Product deleted successfully');
    }
}
