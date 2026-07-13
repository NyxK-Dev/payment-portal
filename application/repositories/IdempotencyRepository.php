<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'interfaces/IdempotencyInterface.php';

class IdempotencyRepository implements IdempotencyInterface
{
    protected $CI;
    protected $table;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Idempotency_model');

        $this->table = $this->CI->Idempotency_model->getTable();
    }

    /**
     * Find Idempotency Key
     */
    public function find($key)
    {
        return $this->CI->db
            ->where(
                'idempotency_key',
                $key
            )
            ->get($this->table)
            ->row();
    }

    /**
     * Create Idempotency Record
     */
    public function create(array $data)
    {
        return $this->CI->db
            ->insert(
                $this->table,
                $data
            );
    }

    /**
     * Mark Request Completed
     */
    public function complete(
        $key,
        $response,
        $code = 200
    ) {
        return $this->CI->db
            ->where(
                'idempotency_key',
                $key
            )
            ->update(
                $this->table,
                [
                    'status'        => 'completed',
                    'response_code' => $code,
                    'response_data' => json_encode($response),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]
            );
    }

    /**
     * Mark Request Failed
     */
    public function fail(
        $key,
        $message
    ) {
        return $this->CI->db
            ->where(
                'idempotency_key',
                $key
            )
            ->update(
                $this->table,
                [
                    'status'        => 'failed',
                    'response_code' => 500,
                    'response_data' => json_encode([
                        'error' => $message
                    ]),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]
            );
    }
}