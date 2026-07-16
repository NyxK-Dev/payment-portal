<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'services/BaseService.php';

class Product_Service extends BaseService
{
    public function __construct()
    {
        $CI = &get_instance();
        $CI->load->repository('ProductRepository');

        parent::__construct($CI->productrepository, 'PRODUCT');
    }

    public function create($request, $userId)
    {
        $product = [
            "name"               => $request['name'] ?? null,
            "description"        => $request['description'] ?? null,
            "sku"                => $request['sku'] ?? null,
            "price"              => $request['price'] ?? 0.00,
            "stock_qty"          => $request['stock_qty'] ?? 0,
            "category_lookup_id" => $request['category_lookup_id'] ?? null,
            "status_lookup_id"   => $request['status_lookup_id'] ?? null,
            "created_by"         => $userId,
            "created_at"         => date('Y-m-d H:i:s')
        ];

        $insertId = $this->repository->insert($product);

        if ($insertId) {
            $this->logAction('CREATE', $insertId, null, $product);
        }

        return $insertId;
    }

    public function update($id, $request)
    {
        $data = [
            "name" => $request['name'],
            "description" => $request['description'],
            "sku" => $request['sku'],
            "price" => $request['price'],
            "stock_qty" => $request['stock_qty'],
            "category_lookup_id" => $request['category_lookup_id'],
            "status_lookup_id" => $request['status_lookup_id'],
            "updated_at" => date('Y-m-d H:i:s')
        ];

        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        $product = $this->repository->find($id);
        if (!$product) return false;

        $result = $this->repository->softDelete($id);

        if ($result) {
            $this->logAction('DELETE', $id, $product, null);
        }

        return $result;
    }

    public function getCreateData()
    {
        return [
            'categories' => $this->repository->getLookupsByGroup('product_category'),
            'statuses'   => $this->repository->getLookupsByGroup('product_status')
        ];
    }
}
