<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuditLogs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->service('AuditLogService');
    }

    public function index()
    {
        $data = [
            'title'   => 'System Audit Trails',
            'content' => 'admin/audit_logs/index',
            'logs'    => $this->auditlogservice->getHistory([], 100, 0)
        ];
        $this->load->view('layouts/app_layout', $data);
    }
}
