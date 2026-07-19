<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/LookupRepositoryInterface.php';
require_once APPPATH . 'repositories/LookupRepository.php';
require_once APPPATH . 'services/BaseService.php';

class LookupService extends BaseService
{
    public function __construct(
        LookupRepositoryInterface $repository,
        AuditLogService $auditService
    ) {
        parent::__construct(
            $repository,
            'LOOKUP',
            $auditService
        );
    }

    public function create(array $data)
    {

        if (empty($data['code'])) {

            throw new InvalidArgumentException(
                'Lookup code is required'
            );
        }


        if (empty($data['value'])) {

            throw new InvalidArgumentException(
                'Lookup value is required'
            );
        }


        if (
            isset($data['is_active']) &&
            !in_array($data['is_active'], [0, 1])
        ) {

            throw new InvalidArgumentException(
                'Invalid active status'
            );
        }


        if (
            isset($data['sort_order']) &&
            $data['sort_order'] < 0
        ) {

            throw new InvalidArgumentException(
                'Invalid sort order'
            );
        }



        $payload = [
            'group_id' => $data['group_id'] ?? null,
            'code' => $data['code'],
            'value' => $data['value'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ];


        $insertId =
            $this->repository
            ->create($payload);


        if ($insertId) {
            $this->logAction(
                'CREATE',
                $insertId,
                null,
                $payload
            );
        }


        return $insertId;
    }

    public function update($id, array $data)
    {
        $oldRecord = $this->repository->find($id);
        if (!$oldRecord) return false;

        $payload = [
            'code'        => $data['code'] ?? $oldRecord->code,
            'value'       => $data['value'] ?? $oldRecord->value,
            'description' => $data['description'] ?? $oldRecord->description,
            'sort_order'  => $data['sort_order'] ?? $oldRecord->sort_order,
            'is_active'   => $data['is_active'] ?? $oldRecord->is_active,
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

    public function getByGroup($groupId)
    {
        return $this->repository->getByGroup($groupId);
    }

    public function getAllWithGroup()
    {
        return $this->repository->getAllWithGroup();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function countByGroup($groupId)
    {
        return $this->repository->countByGroup($groupId);
    }
    public function getByGroupCode($groupCode)
    {
        return $this->repository->getByGroupCode($groupCode);
    }
}
