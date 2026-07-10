<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_log_model extends CI_Model
{
    protected $table = 'audit_logs';

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function getLogs($filters = [], $limit = 100, $offset = 0)
    {
        // Maps users.name to username to match your admin layout views smoothly
        $this->db->select('audit_logs.*, users.email, users.name AS username');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = audit_logs.user_id', 'left');

        if (!empty($filters['user_id'])) $this->db->where('audit_logs.user_id', $filters['user_id']);
        if (!empty($filters['action'])) $this->db->where('audit_logs.action', $filters['action']);
        if (!empty($filters['entity_type'])) $this->db->where('audit_logs.entity_type', $filters['entity_type']);
        if (!empty($filters['entity_id'])) $this->db->where('audit_logs.entity_id', $filters['entity_id']);

        $this->db->order_by('audit_logs.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    public function countLogs($filters = [])
    {
        if (!empty($filters['user_id'])) $this->db->where('user_id', $filters['user_id']);
        if (!empty($filters['action'])) $this->db->where('action', $filters['action']);
        if (!empty($filters['entity_type'])) $this->db->where('entity_type', $filters['entity_type']);
        if (!empty($filters['entity_id'])) $this->db->where('entity_id', $filters['entity_id']);

        return $this->db->count_all_results($this->table);
    }
}
