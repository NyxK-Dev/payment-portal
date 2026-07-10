<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/AuditLogRepositoryInterface.php';
require_once APPPATH . 'repositories/AuditLogRepository.php';

class AuditLogService
{
    protected $repository;
    protected $CI;

    public function __construct()
    {
        $this->repository = new AuditLogRepository();
        $this->CI = &get_instance();
    }

    /**
     * Records an actionable event inside the database layout
     */
    public function log($action, $entityType, $entityId = null, array $oldData = null, array $newData = null)
    {
        // Safely pull current authenticating user session property 
        // Adjust 'user_id' key string to match your exact structural architecture
        $userId = $this->CI->session->userdata('user_id') ?: null;

        $logData = [
            'user_id'     => $userId,
            'action'      => strtoupper($action),
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_data'    => $oldData ? json_encode($oldData) : null,
            'new_data'    => $newData ? json_encode($newData) : null,
            'ip_address'  => $this->CI->input->ip_address(),
            'user_agent'  => $this->CI->input->user_agent(),
            'created_at'  => date('Y-m-d H:i:s')
        ];

        return $this->repository->create($logData);
    }

    public function getHistory(array $filters = [], $limit = 100, $offset = 0)
    {
        return $this->repository->getLogs($filters, $limit, $offset);
    }
}
