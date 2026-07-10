<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface LookupRepositoryInterface
{
    public function getByGroup($groupId);
    public function getAllWithGroup();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function countByGroup($groupId);
}
