<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface LookupGroupRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
}
