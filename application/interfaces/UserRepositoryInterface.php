<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface UserRepositoryInterface
{
    public function findById(int $id);

    public function findByEmail(string $email);

    public function getAll(int $limit = 20, int $offset = 0);

    public function find(int $id);

    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function getRoleByName(string $name);

    public function updateRole(int $id, int $roleId): bool;

    public function updateLastLogin(int $id): bool;
}