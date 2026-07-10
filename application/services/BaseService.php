<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'services/AuditLogService.php';

abstract class BaseService
{
    protected $repository;
    protected $auditService;
    protected $entityType;

    public function __construct($repository, $entityType)
    {
        $this->repository = $repository;
        $this->entityType = strtoupper($entityType);
        $this->auditService = new AuditLogService();
    }

    /**
     * Helper to quickly write audit logs from any inheriting child service.
     */
    protected function logAction($action, $entityId, $oldData = null, $newData = null)
    {
        return $this->auditService->log(
            strtoupper($action),
            $this->entityType,
            $entityId,
            $oldData ? (array) $oldData : null,
            $newData ? (array) $newData : null
        );
    }
}
