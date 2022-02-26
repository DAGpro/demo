<?php

namespace App\Core\Component\IdentityAccess\Access\Application\Service;

use App\Core\Component\IdentityAccess\Access\Domain\Exception\AssignedItemException;

interface AssignAccessServiceInterface
{
    /**
     * @throws AssignedItemException
     */
    public function assignRole(RoleDTO $roleDTO, string|int $userId): void;

    /**
     * @throws AssignedItemException
     */
    public function assignPermission(PermissionDTO $permissionDTO, string|int $userId): void;

    public function revokeRole(RoleDTO $roleDTO, string|int $userId): void;

    public function revokePermission(PermissionDTO $permissionDTO, string|int $userId): void;

    public function revokeAll(string|int $userId): void;

    public function clearAssignments(): void;
}
