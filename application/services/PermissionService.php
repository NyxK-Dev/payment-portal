<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'services/BaseService.php';

class PermissionService extends BaseService
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        // 1. Load the custom repository extension layout
        $this->CI->load->repository('PermissionRepository');

        // 2. Supply the core repository and identity label directly to the parent layout
        parent::__construct($this->CI->permissionrepository, 'PERMISSION');
    }

    public function create($data)
    {
        if (empty($data['code'])) {
            throw new Exception('Permission code is required');
        }

        if (empty($data['name'])) {
            throw new Exception('Permission name is required');
        }

        if ($this->repository->existsCode($data['code'])) {
            throw new Exception('Permission code already exists');
        }

        // 3. Explicit secure payload compilation mapping
        $payload = [
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'created_at'  => date('Y-m-d H:i:s')
        ];

        $insertId = $this->repository->create($payload);

        // 4. Log creation if the record was successfully saved
        if ($insertId) {
            $this->logAction('CREATE', $insertId, null, $payload);
        }

        return $insertId;
    }

    public function update($id, $data)
    {
        // Fetch current snapshot before modifying database rows
        $oldRecord = $this->repository->find($id);
        if (!$oldRecord) {
            return false;
        }

        if (isset($data['code']) && $this->repository->existsCode($data['code'], $id)) {
            throw new Exception('Permission code already exists');
        }

        // Explicit payload mapping to ensure we don't clear data accidentally
        $payload = [
            'code'        => $data['code'] ?? $oldRecord->code,
            'name'        => $data['name'] ?? $oldRecord->name,
            'description' => $data['description'] ?? $oldRecord->description,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $result = $this->repository->update($id, $payload);

        // 5. Fire log showing exactly what changed
        if ($result) {
            $this->logAction('UPDATE', $id, $oldRecord, $payload);
        }

        return $result;
    }

    public function delete($id)
    {
        // Capture old permissions data layout blueprint before deleting
        $oldRecord = $this->repository->find($id);
        if (!$oldRecord) {
            return false;
        }

        $result = $this->repository->delete($id);

        // 6. Push snapshot down into history trails upon deletion
        if ($result) {
            $this->logAction('DELETE', $id, $oldRecord, null);
        }

        return $result;
    }

    // --- Custom non-mutating select wrappers clean and untouched ---
    public function getPermissions()
    {
        return $this->repository->getAll();
    }

    public function getPermission($id)
    {
        return $this->repository->find($id);
    }
}
