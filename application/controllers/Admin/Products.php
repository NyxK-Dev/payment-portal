<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Products extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->service('Product_Service');

        $this->load->library('RequestValidator');

        $this->load->repository('Product_Repository');
    }



    public function index()
    {
        $keyword = $this->input->get('keyword');

        $data['products'] = $this->product_repository->all([
            'keyword' => $keyword
        ]);

        $data['title'] = '';
        $data['page_heading'] = 'Products';
        $data['page_description'] = 'Manage products';
        $data['breadcrumbs'] = [
            'Home' => '',
            'Products' => NULL,
        ];

        $this->render('admin/products/index', $data);
    }

    public function create()
    {
        $data = $this->product_service->getCreateData();

        $data['title'] = '';
        $data['page_heading'] = 'Create Product';
        $data['page_description'] = 'Add a new product';
        $data['breadcrumbs'] = [
            'Home' => '',
            'Products' => site_url('admin/products'),
            'Create' => NULL,
        ];

        $this->render('admin/products/create', $data);
    }

    public function store()
{
    if (!$this->requestvalidator->validate('Product', 'create')) {

        $data = $this->product_service
            ->getCreateData();

        $data['errors'] = $this->requestvalidator->errors();

        $data['title'] = '';
        $data['page_heading'] = 'Create Product';
        $data['page_description'] = 'Add a new product';

        $data['breadcrumbs'] = [
            'Home' => '',
            'Products' => site_url('admin/products'),
            'Create' => NULL,
        ];

        $this->render(
            'admin/products/create',
            $data
        );

        return;
    }


    $request = $this->input->post();


    $userId = $this->session->userdata('user_id');


    $this->product_service
        ->create(
            $request,
            $userId
        );


    redirect('admin/products');
}


    public function edit($id)
    {
        $data = $this->product_service->getCreateData();

        $data['product'] = $this->product_repository->find($id);

        $data['title'] = '';
        $data['page_heading'] = 'Edit Product';
        $data['page_description'] = 'Update product information';
        $data['breadcrumbs'] = [
            'Home' => '',
            'Products' => site_url('admin/products'),
            'Edit' => NULL,
        ];

        $this->render('admin/products/edit', $data);
    }

    public function update($id)
{
    if (!$this->requestvalidator->validate('Product', 'update')) {

        $data = $this->product_service
            ->getCreateData();


        $data['product'] =
            $this->product_repository->find($id);


        $data['errors'] =
            $this->requestvalidator->errors();


        $data['title'] = '';
        $data['page_heading'] = 'Edit Product';
        $data['page_description'] = 'Update product information';


        $data['breadcrumbs'] = [
            'Home' => '',
            'Products' => site_url('admin/products'),
            'Edit' => NULL,
        ];


        $this->render(
            'admin/products/edit',
            $data
        );

        return;
    }


    $request = $this->input->post();


    $this->product_service
        ->update(
            $id,
            $request
        );


    redirect('admin/products');
}

    public function show($id)
    {
        $product = $this->product_repository->find($id);

        $this->render('admin/products/show', [
            'title' => '',
            'page_heading' => 'Product Details',
            'page_description' => 'View product information',
            'breadcrumbs' => [
                'Home' => '',
                'Products' => site_url('admin/products'),
                'Details' => NULL,
            ],
            'product' => $product,
        ]);
    }

    public function destroy($id)
    {
        $this->product_service->delete($id);

        redirect('admin/products');
    }

}