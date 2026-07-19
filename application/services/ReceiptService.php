<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'interfaces/ReceiptRepositoryInterface.php';
require_once APPPATH . 'repositories/ReceiptRepository.php';
require_once APPPATH . 'services/BaseService.php';

class ReceiptService extends BaseService
{
    public function __construct(
        ReceiptRepositoryInterface $repository,
        AuditLogService $auditService
    ) {
        parent::__construct(
            $repository,
            'RECEIPT',
            $auditService
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function getAllWithRelations()
    {
        return $this->repository->getAllWithRelations();
    }

    public function findWithRelations($id)
    {
        return $this->repository->findWithRelations($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    public function getCustomerReceipts($userId)
    {
        return $this->repository->getByUser($userId);
    }

    public function getCustomerReceipt(
        $receiptId,
        $userId
    ) {
        return $this->repository->findByUser(
            $receiptId,
            $userId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {

        if (!isset($data['invoice_id'])) {
            throw new InvalidArgumentException(
                'Invoice ID is required'
            );
        }


        if (!isset($data['receipt_no'])) {
            throw new InvalidArgumentException(
                'Receipt number is required'
            );
        }


        if (!isset($data['amount'])) {
            throw new InvalidArgumentException(
                'Amount is required'
            );
        }


        if (!isset($data['status_lookup_id'])) {
            throw new InvalidArgumentException(
                'Status is required'
            );
        }


        if (!isset($data['issued_by'])) {
            throw new InvalidArgumentException(
                'Issued by is required'
            );
        }



        $payload = [

            'invoice_id' => $data['invoice_id'],

            'receipt_no' => $data['receipt_no'],

            'amount' => $data['amount'],

            'status_lookup_id' => $data['status_lookup_id'],

            'issued_at' => $data['issued_at']
                ?? date('Y-m-d H:i:s'),

            'issued_by' => $data['issued_by'],

            'created_at' => date('Y-m-d H:i:s')

        ];


        return $this->repository->create($payload);
    }

    public function update($id, array $data)
    {
        $payload = [];

        foreach ($data as $key => $value) {
            $payload[$key] = $value;
        }

        $payload['updated_at'] =
            date('Y-m-d H:i:s');

        return $this->repository->update(
            $id,
            $payload
        );
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
