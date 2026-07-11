<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_model extends CI_Model
{
    protected $table = 'invoices';

    public function find($id)
    {
        return $this->db
            ->select('
    invoices.*,
    lookups.code AS status_code,
    lookups.value AS status_name,
    orders.order_no,
    users.email AS customer_email
')
            ->from($this->table)
            ->join('orders', 'orders.id = invoices.order_id', 'inner')
            ->join('users', 'users.id = orders.user_id', 'inner')
            ->join('lookups', 'lookups.id = invoices.status_lookup_id', 'left')
            ->where('invoices.id', $id)
            ->get()
            ->row();
    }
    public function getOrderItems($orderId)
    {
        return $this->db
            ->select('
            order_items.*,
            products.name AS product_name,
            products.description AS product_description,
            products.sku
        ')
            ->from('order_items')
            ->join('products', 'products.id = order_items.product_id', 'inner')
            ->where('order_items.order_id', $orderId)
            ->get()
            ->result();
    }

    public function findByInvoiceNo($invoiceNo)
    {
        return $this->db->where('invoice_no', $invoiceNo)->get($this->table)->row();
    }

    public function findByOrderId($orderId)
    {
        return $this->db->where('order_id', $orderId)->get($this->table)->row();
    }

    public function getFilteredInvoices(array $filters)
    {
        $this->db->select('
        invoices.*,
        lookups.code AS status_code,
        lookups.value AS status_name,
        orders.order_no,
        users.name AS customer_name,
        issuer.name AS issuer_name
    ')
            ->from($this->table)
            ->join('orders', 'orders.id = invoices.order_id', 'inner')
            ->join('users', 'users.id = orders.user_id', 'inner')
            ->join('lookups', 'lookups.id = invoices.status_lookup_id', 'left')
            ->join('users issuer', 'issuer.id = invoices.issued_by', 'left');

        if (!empty($filters['user_id'])) {
            $this->db->where('orders.user_id', $filters['user_id']);
        }

        if (!empty($filters['status_lookup_id'])) {
            $this->db->where('invoices.status_lookup_id', $filters['status_lookup_id']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('invoices.invoice_no', $filters['search'])
                ->or_like('orders.order_no', $filters['search'])
                ->group_end();
        }

        return $this->db
            ->order_by('invoices.created_at', 'DESC')
            ->get()
            ->result();
    }

    public function create(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
