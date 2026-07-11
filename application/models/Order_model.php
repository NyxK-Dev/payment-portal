<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{
    protected $table = 'orders';

    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    public function findByOrderNo($orderNo)
    {
        return $this->db
            ->where('order_no', $orderNo)
            ->get($this->table)
            ->row();
    }

    public function update($id, array $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }


    public function getByUser(
        $userId,
        $filters = []
    ) {

        $this->db
            ->select('orders.*')
            ->from('orders')
            ->where(
                'orders.user_id',
                $userId
            );



        if (!empty($filters['keyword'])) {

            $this->db->like(
                'orders.order_no',
                $filters['keyword']
            );
        }



        if (!empty($filters['from'])) {

            $this->db->where(
                'DATE(orders.created_at) >=',
                $filters['from']
            );
        }



        if (!empty($filters['to'])) {

            $this->db->where(
                'DATE(orders.created_at) <=',
                $filters['to']
            );
        }



        return $this->db
            ->order_by(
                'orders.created_at',
                'DESC'
            )
            ->get()
            ->result();
    }

    public function getAll()
    {
        return $this->db
            ->select([
                'orders.id',
                'orders.order_no',
                'orders.user_id',
                'orders.total_amount',
                'orders.status_lookup_id',
                'orders.created_at',
                'users.name AS customer_name',
                'lookups.value AS status_name'
            ])
            ->from('orders')
            ->join(
                'users',
                'users.id = orders.user_id',
                'left'
            )
            ->join(
                'lookups',
                'lookups.id = orders.status_lookup_id',
                'left'
            )
            ->order_by('orders.created_at', 'DESC')
            ->get()
            ->result();
    }

    public function findWithItems($id)
    {
        return $this->db
            ->select([
                'orders.id',
                'orders.user_id',
                'orders.order_no',
                'orders.total_amount',
                'orders.status_lookup_id',
                'orders.created_at',

                'users.name AS customer_name',
                'users.email',

                'lookups.value AS status_name'
            ])
            ->from($this->table)

            ->join(
                'users',
                'users.id = orders.user_id',
                'left'
            )

            ->join(
                'lookups',
                'lookups.id = orders.status_lookup_id',
                'left'
            )

            ->where(
                'orders.id',
                (int)$id
            )

            ->limit(1)

            ->get()
            ->row();
    }
}
