<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface PermissionRepositoryInterface
{
    public function getAll();

    public function find(int $id);

    public function existsCode(string $code, int $ignoreId = null): bool;

    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}