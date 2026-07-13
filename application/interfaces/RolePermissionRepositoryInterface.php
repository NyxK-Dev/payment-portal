<?php
defined('BASEPATH') or exit('No direct script access allowed');

interface RolePermissionRepositoryInterface
{
    public function getAll();

    public function insert(array $data): bool;

    public function deleteByRole(int $roleId): bool;

    public function getPermissionIdsByRole(int $roleId): array;

    public function hasPermission(int $roleId, string $permission): bool;
}