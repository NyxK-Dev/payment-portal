<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Product_model');
        $this->load->library('session');
    }

    public function index()
{
    $cart = $this->session->userdata('cart') ?? [];

    $this->render(
        'user/products/cart',
        [
            'title' => '',
            'cart'  => $cart
        ]
    );
}

    public function add()
    {
        $productId = (int)$this->input->post('product_id');
        $qty       = (int)$this->input->post('quantity');

        if ($qty < 1) {
            $qty = 1;
        }

        $product = $this->Product_model->find($productId);

        if (!$product) {
            show_404();
        }

        $cart = $this->session->userdata('cart') ?? [];

        if (isset($cart[$productId])) {

            $newQty = $cart[$productId]['quantity'] + $qty;

            $cart[$productId]['quantity'] = min(
                $newQty,
                $product->stock_qty
            );

        } else {

            $cart[$productId] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'sku'        => $product->sku,
                'price'      => $product->price,
                'quantity'   => min($qty, $product->stock_qty),
                'stock_qty'  => $product->stock_qty
            ];
        }

        $this->session->set_userdata('cart', $cart);

        redirect('user/cart');
    }

   public function increase($productId)
{
    $cart = $this->session->userdata('cart') ?? [];

    if (isset($cart[$productId])) {

        if (
            $cart[$productId]['quantity']
            < $cart[$productId]['stock_qty']
        ) {
            $cart[$productId]['quantity']++;
        }

        $this->session->set_userdata('cart', $cart);
    }

    redirect('user/cart');
}

public function decrease($productId)
{
    $cart = $this->session->userdata('cart') ?? [];

    if (isset($cart[$productId])) {

        $cart[$productId]['quantity']--;

        if ($cart[$productId]['quantity'] <= 0) {

            unset($cart[$productId]);
        }

        $this->session->set_userdata('cart', $cart);
    }

    redirect('user/cart');
}

    public function remove($productId)
    {
        $cart = $this->session->userdata('cart') ?? [];

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        $this->session->set_userdata('cart', $cart);

        redirect('user/cart');
    }

    public function clear()
    {
        $this->session->unset_userdata('cart');

        redirect('user/cart');
    }

    public function checkout()
{
    $cart = $this->session->userdata('cart') ?? [];

    if (empty($cart)) {

        $this->session->set_flashdata(
            'error',
            'Your cart is empty.'
        );

        redirect('user/cart');
    }

    $this->render(
        'user/products/checkout',
        [
            'title' => 'Checkout',
            'cart'  => $cart
        ]
    );
}
}