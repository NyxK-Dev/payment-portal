<?php

defined('BASEPATH') or exit('No direct script access allowed');


class AuditLogService
{
    protected $repository;
    protected $CI;


    public function __construct(
        AuditLogRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        $this->CI = &get_instance();
    }


    /**
     * Records an actionable event inside the database layout
     */
    public function log(
        $action,
        $entityType,
        $entityId = null,
        array $oldData = null,
        array $newData = null
    ) {

        $userId = null;
        $ipAddress = null;
        $userAgent = null;


        if ($this->CI) {

            if (isset($this->CI->session)) {
                $userId =
                    $this->CI
                    ->session
                    ->userdata('user_id');
            }


            if (isset($this->CI->input)) {
                $ipAddress =
                    $this->CI
                    ->input
                    ->ip_address();

                $userAgent =
                    $this->CI
                    ->input
                    ->user_agent();
            }
        }


        $logData = [
            'user_id'     => $userId,
            'action'      => strtoupper($action),
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_data'    => $oldData
                ? json_encode($oldData)
                : null,

            'new_data'    => $newData
                ? json_encode($newData)
                : null,

            'ip_address'  => $ipAddress,
            'user_agent'  => $userAgent,
            'created_at'  => date('Y-m-d H:i:s')
        ];


        return $this->repository->create(
            $logData
        );
    }


    public function getHistory(
        array $filters = [],
        $limit = 100,
        $offset = 0
    ) {
        return $this->repository
            ->getLogs(
                $filters,
                $limit,
                $offset
            );
    }
    
}
