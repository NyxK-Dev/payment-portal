<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'services/BaseService.php';


class PermissionService extends BaseService
{

    public function __construct(
        PermissionRepositoryInterface $repository,
        AuditLogService $auditService
    ) {
        parent::__construct(
            $repository,
            'PERMISSION',
            $auditService
        );
    }
    public function create($data)
    {

        if (empty($data['code'])) {
            throw new Exception(
                'Permission code is required'
            );
        }


        if (empty($data['name'])) {
            throw new Exception(
                'Permission name is required'
            );
        }



        if (
            $this->repository
            ->existsCode($data['code'])
        ) {
            throw new Exception(
                'Permission code already exists'
            );
        }



        $payload = [
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'created_at'  => date('Y-m-d H:i:s')
        ];



        $insertId = $this->repository
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

    public function update($id, $data)
    {

        $oldRecord = $this->repository
            ->find($id);



        if (!$oldRecord) {
            return false;
        }



        if (
            isset($data['code']) &&
            $this->repository
            ->existsCode(
                $data['code'],
                $id
            )
        ) {

            throw new Exception(
                'Permission code already exists'
            );
        }




        $payload = [
            'code'        => $data['code'] ?? $oldRecord->code,
            'name'        => $data['name'] ?? $oldRecord->name,
            'description' => $data['description'] ?? $oldRecord->description,
            'updated_at'  => date('Y-m-d H:i:s')
        ];




        $result = $this->repository
            ->update(
                $id,
                $payload
            );




        if ($result) {

            $this->logAction(
                'UPDATE',
                $id,
                $oldRecord,
                $payload
            );
        }



        return $result;
    }


    public function delete($id)
    {

        $oldRecord = $this->repository
            ->find($id);



        if (!$oldRecord) {
            return false;
        }



        $result = $this->repository
            ->delete($id);



        if ($result) {

            $this->logAction(
                'DELETE',
                $id,
                $oldRecord,
                null
            );
        }



        return $result;
    }


    public function getPermissions()
    {
        return $this->repository
            ->getAll();
    }

    public function getPermission($id)
    {
        return $this->repository
            ->find($id);
    }
}
