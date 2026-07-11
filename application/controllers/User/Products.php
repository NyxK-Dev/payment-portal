<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->repository('ProductRepository');
    }

    public function index()
    {
        $keyword = $this->input->get('keyword');

        $products = $this->productrepository
            ->getActiveProducts($keyword);

        $this->render(
            'user/products/index',
            [
                'title' => '',
                'products' => $products
            ]
        );
    }

    public function show($id)
    {
        $product = $this->productrepository
            ->findActiveProduct($id);

        if (!$product)
        {
            show_404();
        }

        $this->render(
            'user/products/show',
            [
                'title' => '',
                'product' => $product
            ]
        );
    }
}