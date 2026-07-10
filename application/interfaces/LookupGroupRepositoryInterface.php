<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface LookupGroupRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
