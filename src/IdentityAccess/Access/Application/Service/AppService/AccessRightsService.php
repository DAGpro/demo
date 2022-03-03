<?php

declare(strict_types=1);

namespace App\IdentityAccess\Access\Application\Service\AppService;

use App\IdentityAccess\Access\Application\Service\AccessRightsServiceInterface;
use App\IdentityAccess\Access\Application\Service\PermissionDTO;
use App\IdentityAccess\Access\Application\Service\RoleDTO;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Manager;

final class AccessRightsService implements AccessRightsServiceInterface
{
    private Manager $manager;
    private ItemsStorageInterface $storage;

    public function __construct(
        Manager $manager,
        ItemsStorageInterface $storage
    ) {
        $this->manager = $manager;
        $this->storage = $storage;
    }

    public function existRole(string $roleName): bool
    {
        return $this->storage->getRole($roleName) !== null;
    }

    public function getRoleByName(string $roleName): ?RoleDTO
    {
        $role = $this->storage->getRole($roleName);
        if ($role !== null) {
            $roleDTO = new RoleDTO($role->getName());
            $roleDTO->withChildRoles($this->getChildRoles($roleDTO));
            $roleDTO->withNestedRoles($this->getNestedRoles($roleDTO));
            $roleDTO->withChildPermissions($this->getPermissionsByRole($roleDTO));
            $roleDTO->withNestedPermissions($this->getNestedPermissionsByRole($roleDTO));
            return $roleDTO;
        }

        return null;
    }

    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->storage->getRoles() as $role) {
            $roleDTO = new RoleDTO($role->getName());
            $roleDTO->withChildRoles($this->getChildRoles($roleDTO));
            $roleDTO->withNestedRoles($this->getNestedRoles($roleDTO));
            $roleDTO->withChildPermissions($this->getPermissionsByRole($roleDTO));
            $roleDTO->withNestedPermissions($this->getNestedPermissionsByRole($roleDTO));

            $roles[$role->getName()] = $roleDTO;
        }
        return $roles;
    }

    public function existPermission(string $permissionName): bool
    {
        return $this->getPermissionByName($permissionName) !== null;
    }

    public function getPermissionByName(string $permissionName): ?PermissionDTO
    {
        $permission = $this->storage->getPermission($permissionName);
        return $permission === null ? null : new PermissionDTO($permission->getName());
    }

    public function getPermissions(): array
    {
        $permissionDTO = [];
        foreach ($this->storage->getPermissions() as $permission) {
            $permissionName = $permission->getName();
            $permissionDTO[$permissionName] = new PermissionDTO($permissionName);
        }
        return $permissionDTO;
    }

    public function getChildRoles(RoleDTO $roleDTO): array
    {
        $roles = [];
        foreach ($this->storage->getChildren($roleDTO->getName()) as $role) {
            if ($this->getRoleByName($role->getName())) {
                if ($roleDTO->getName() === $role->getName()) {
                    continue;
                }
                $roleName = $role->getName();
                $roles[$roleName] = new RoleDTO($roleName);
            }
        }
        return $roles;
    }

    public function getNestedRoles(RoleDTO $roleDTO): array
    {
        $roles = [];
        $childRoles = $this->storage->getChildren($roleDTO->getName());
        foreach ($this->manager->getChildRoles($roleDTO->getName()) as $role) {
            if (array_key_exists($role->getName(), $childRoles) || $roleDTO->getName() === $role->getName()) {
                continue;
            }
            $roleName = $role->getName();
            $roles[$roleName] = new RoleDTO($roleName);
        }
        return $roles;
    }

    public function getPermissionsByRole(RoleDTO $roleDTO): array
    {
        $permissions = [];
        foreach ($this->storage->getChildren($roleDTO->getName()) as $permission) {
            if ($this->getRoleByName($permission->getName())) {
                continue;
            }
            $permissionName = $permission->getName();
            $permissions[$permissionName] = new PermissionDTO($permissionName);
        }
        return $permissions;
    }

    public function getNestedPermissionsByRole(RoleDTO $roleDTO): array
    {
        $permissions = [];
        $childPermissions = $this->storage->getChildren($roleDTO->getName());
        foreach ($this->manager->getPermissionsByRoleName($roleDTO->getName()) as $permission) {
            if (array_key_exists($permission->getName(), $childPermissions)) {
                continue;
            }
            $permissionName = $permission->getName();
            $permissions[$permissionName] = new PermissionDTO($permissionName);
        }
        return $permissions;
    }

    public function hasChildren(RoleDTO $parentDTO): bool
    {
        return $this->storage->hasChildren($parentDTO->getName());
    }

    public function setDefaultRoles(array $roles): self
    {
        $this->manager->setDefaultRoleNames($roles);
        return $this;
    }

    public function getDefaultRoleNames(): array
    {
        return $this->manager->getDefaultRoleNames();
    }

    public function getDefaultRoles(): array
    {
        return $this->manager->getDefaultRoles();
    }

}
