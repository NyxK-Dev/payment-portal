<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface IdempotencyInterface
{
    /**
     * Find Idempotency Key
     */
    public function find($key);

    /**
     * Create Idempotency Record
     */
    public function create(array $data);

    /**
     * Mark Request Completed
     */
    public function complete($key, $response, $code = 200);

    /**
     * Mark Request Failed
     */
    public function fail($key, $message);
}