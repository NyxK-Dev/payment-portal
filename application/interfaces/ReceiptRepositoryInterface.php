<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface ReceiptRepositoryInterface
{
    public function getAllWithRelations();
    public function find($id);
    public function findWithRelations($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
