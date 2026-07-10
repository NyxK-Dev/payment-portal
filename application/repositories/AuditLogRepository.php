<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/AuditLogRepositoryInterface.php';

class AuditLogRepository implements AuditLogRepositoryInterface
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Audit_log_model');
    }

    public function create(array $data)
    {
        return $this->CI->Audit_log_model->create($data);
    }

    public function getLogs(array $filters = [], $limit = 100, $offset = 0)
    {
        return $this->CI->Audit_log_model->getLogs($filters, $limit, $offset);
    }

    public function countLogs(array $filters = [])
    {
        return $this->CI->Audit_log_model->countLogs($filters);
    }
}
