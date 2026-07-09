<?php


class Product_Service
{
    protected $repository;

    public function __construct()
    {
        $CI =& get_instance();

        $CI->load->repository('Product_Repository');

        $this->repository = $CI->product_repository;

    }

    public function create($request, $userId)
    {
        $product = [
            "name" => $request['name'],
            "description" => $request['description'],
            "sku" => $request['sku'],
            "price" => $request['price'],
            "stock_qty" => $request['stock_qty'],
            "category_lookup_id" => $request['category_lookup_id'],
            "status_lookup_id" => $request['status_lookup_id'],
            "created_by" => $userId,
            "created_at" => date('Y-m-d H:i:s')
        ];

        return $this->repository->create($product);
    }

    public function update($id, $request)
    {
        $data = [
            "name" => $request['name'],
            "description" => $request['description'],
            "price" => $request['price'],
            "stock_qty" => $request['stock_qty'],
            "updated_at" => date('Y-m-d H:i:s')
        ];

        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        $product = $this->repository->find($id);

        if (!$product) {
            return false;
        }

        return $this->repository->delete($id);
    }

    public function getCreateData()
    {
        return [
            'categories' => $this->repository
                ->getLookupsByGroup('product_category'),

            'statuses' => $this->repository
                ->getLookupsByGroup('product_status')
        ];
    }


}