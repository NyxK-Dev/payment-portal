<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/ReceiptRepositoryInterface.php';
require_once APPPATH . 'repositories/ReceiptRepository.php';
require_once APPPATH . 'services/BaseService.php';

class ReceiptService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new ReceiptRepository(), 'RECEIPT');
    }

    public function create(array $data)
    {
        $payload = [
            'invoice_id'       => $data['invoice_id'] ?? null,
            'receipt_no'       => $data['receipt_no'] ?? null,
            'amount'           => $data['amount'] ?? null,
            'status_lookup_id' => $data['status_lookup_id'] ?? null,
            'issued_at'        => $data['issued_at'] ?? date('Y-m-d H:i:s'),
            'issued_by'        => $data['issued_by'] ?? null,
            'created_at'       => date('Y-m-d H:i:s')
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
            'invoice_id'       => $data['invoice_id'] ?? $oldRecord->invoice_id,
            'receipt_no'       => $data['receipt_no'] ?? $oldRecord->receipt_no,
            'amount'           => $data['amount'] ?? $oldRecord->amount,
            'status_lookup_id' => $data['status_lookup_id'] ?? $oldRecord->status_lookup_id,
            'issued_at'        => $data['issued_at'] ?? $oldRecord->issued_at,
            'issued_by'        => $data['issued_by'] ?? $oldRecord->issued_by,
            'updated_at'       => date('Y-m-d H:i:s')
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

    public function getAllWithRelations()
    {
        return $this->repository->getAllWithRelations();
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findWithRelations($id)
    {
        return $this->repository->findWithRelations($id);
    }
}
