<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface RoleRepositoryInterface
{
    public function getAll(): array;

    public function find(int $id);

    public function existsName(string $name, ?int $ignoreId = null): bool;

    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}