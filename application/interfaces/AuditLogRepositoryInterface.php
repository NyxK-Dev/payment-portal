<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface AuditLogRepositoryInterface
{
    public function create(array $data);
    public function getLogs(array $filters = [], $limit = 100, $offset = 0);
    public function countLogs(array $filters = []);
}
