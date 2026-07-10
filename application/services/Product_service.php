<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'services/BaseService.php';

class Product_Service extends BaseService
{
    public function __construct()
    {
        $CI = &get_instance();
        $CI->load->repository('Product_Repository');

        parent::__construct($CI->product_repository, 'PRODUCT');
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

        $insertId = $this->repository->create($product);

        if ($insertId) {
            $this->logAction('CREATE', $insertId, null, $product);
        }

        return $insertId;
    }

    public function update($id, $request)
    {
        $oldRecord = $this->repository->find($id);
        if (!$oldRecord) return false;

        $data = [
            "name"        => $request['name'] ?? $oldRecord->name,
            "description" => $request['description'] ?? $oldRecord->description,
            "price"       => $request['price'] ?? $oldRecord->price,
            "stock_qty"   => $request['stock_qty'] ?? $oldRecord->stock_qty,
            "updated_at"  => date('Y-m-d H:i:s')
        ];

        $result = $this->repository->update($id, $data);

        if ($result) {
            $this->logAction('UPDATE', $id, $oldRecord, $data);
        }

        return $result;
    }

    public function delete($id)
    {
        $product = $this->repository->find($id);
        if (!$product) return false;

        $result = $this->repository->delete($id);

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
