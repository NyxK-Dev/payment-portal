<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupGroupRepositoryInterface.php';
require_once APPPATH . 'repositories/LookupGroupRepository.php';
require_once APPPATH . 'services/BaseService.php';

class LookupGroupService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new LookupGroupRepository(), 'LOOKUP_GROUP');
    }

    public function create(array $data)
    {
        $payload = [
            'code'        => $data['code'] ?? null,
            'name'        => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'created_at'  => date('Y-m-d H:i:s')
        ];

        $insertId = $this->repository->create($payload);

        if ($insertId) {
            $this->logAction('CREATE', $insertId, null, $payload);
        }

        return $insertId;
    }

    public function update($id, array $data)
    {
        $oldRecord = $this->repository->find($id);
        if (!$oldRecord) return false;

        $payload = [
            'code'        => $data['code'] ?? $oldRecord->code,
            'name'        => $data['name'] ?? $oldRecord->name,
            'description' => $data['description'] ?? $oldRecord->description,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $result = $this->repository->update($id, $payload);

        if ($result) {
            $this->logAction('UPDATE', $id, $oldRecord, $payload);
        }

        return $result;
    }

    public function delete($id)
    {
        $oldRecord = $this->repository->find($id);
        if (!$oldRecord) return false;

        $result = $this->repository->delete($id);

        if ($result) {
            $this->logAction('DELETE', $id, $oldRecord, null);
        }

        return $result;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }
}
