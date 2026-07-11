<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receipt_model extends CI_Model
{
    protected $table = 'receipts';

    public function getAllWithRelations()
    {
        return $this->db
            ->select('
                receipts.*, 
                invoices.invoice_no, 
                lookups.code AS status_name, 
                users.name AS issuer_name
            ')
            ->from($this->table)
            ->join('invoices', 'invoices.id = receipts.invoice_id', 'left')
            ->join('lookups', 'lookups.id = receipts.status_lookup_id', 'left')
            ->join('users', 'users.id = receipts.issued_by', 'left')
            ->order_by('receipts.created_at', 'DESC')
            ->get()
            ->result();
    }

    public function find($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function findWithRelations($id)
    {
        return $this->db
            ->select('
                receipts.*, 
                invoices.invoice_no, 
                lookups.code as status_name, 
                users.name as issuer_name
            ')
            ->from($this->table)
            ->join('invoices', 'invoices.id = receipts.invoice_id', 'left')
            ->join('lookups', 'lookups.id = receipts.status_lookup_id', 'left')
            ->join('users', 'users.id = receipts.issued_by', 'left')
            ->where('receipts.id', $id)
            ->get()
            ->row();
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
    /**
     * Get all receipts for a customer.
     */
    public function getByUser($userId)
    {
        return $this->db
            ->select("
                receipts.*,
                invoices.invoice_no,
                lookups.code AS status_code,
                lookups.value AS status_name
            ")
            ->from('receipts')
            ->join('invoices', 'invoices.id = receipts.invoice_id', 'left')
            ->join('orders', 'orders.id = invoices.order_id', 'left')
            ->join('lookups', 'lookups.id = receipts.status_lookup_id', 'left')
            ->where('orders.user_id', $userId)
            ->order_by('receipts.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get one receipt that belongs to a customer.
     */
    public function findByUser($receiptId, $userId)
    {
        return $this->db
            ->select("
                receipts.*,
                invoices.invoice_no,
                users.email AS customer_email,
                lookups.code AS status_code,
                lookups.value AS status_name,
                issuer.name AS issuer_name
            ")
            ->from('receipts')
            ->join('invoices', 'invoices.id = receipts.invoice_id', 'left')
            ->join('orders', 'orders.id = invoices.order_id', 'left')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->join('lookups', 'lookups.id = receipts.status_lookup_id', 'left')
            ->join('users issuer', 'issuer.id = receipts.issued_by', 'left')
            ->where('receipts.id', $receiptId)
            ->where('orders.user_id', $userId)
            ->get()
            ->row();
    }
}
