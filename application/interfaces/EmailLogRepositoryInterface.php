<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface EmailLogRepositoryInterface
{
    public function create(array $data);

    public function find($id);

    public function all(array $filters = []);

    public function updateStatus($id, $statusLookupId);
}