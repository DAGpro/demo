<?php

declare(strict_types=1);

namespace App\IdentityAccess\Access\Application\Service\AppService;

use App\IdentityAccess\Access\Application\Service\AccessRightsServiceInterface;
use App\IdentityAccess\Access\Application\Service\AssignmentsServiceInterface;
use App\IdentityAccess\Access\Application\Service\PermissionDTO;
use App\IdentityAccess\Access\Application\Service\RoleDTO;
use App\IdentityAccess\Access\Application\Service\UserAssignmentsDTO;
use App\IdentityAccess\Access\Domain\Exception\NotExistItemException;
use App\IdentityAccess\User\Application\Service\UserQueryServiceInterface;
use App\IdentityAccess\User\Domain\User;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\Manager;

final class AssignmentsService implements AssignmentsServiceInterface
{

    public function __construct(
        private AssignmentsStorageInterface $assignmentsStorage,
        private AccessRightsServiceInterface $accessRightsService,
        private UserQueryServiceInterface $userQueryService,
        private Manager $manager
    ) {
    }


    public function getUserIdsByRole(RoleDTO $roleDTO): array
    {
        return $this->manager->getUserIdsByRoleName($roleDTO->getName());
    }

    public function getRolesByUser(string|int $userId): array
    {
        $roles = [];
        foreach ($this->manager->getRolesByUserId($userId) as $role) {
            $roleDTO = $this->accessRightsService->getRoleByName($role->getName());
            $roles[$role->getName()] = $roleDTO;
        }
        return $roles;
    }

    public function getPermissionsByUser(string|int $userId): array
    {
        $permissions = [];
        foreach ($this->manager->getPermissionsByUserId($userId) as $permission) {
            $permissionName = $permission->getName();
            $permissions[$permissionName] = new PermissionDTO($permissionName);
        }
        return $permissions;
    }

    public function userHasPermission(string|int $userId, string $permissionName): bool
    {
        return $this->manager->userHasPermission($userId, $permissionName);
    }

    public function userHasRole(string|int $userId, string $roleName): bool
    {
        return $this->assignmentsStorage->get($roleName, (string)$userId) !== null;
    }

    public function isAssignedRoleToUsers(RoleDTO $roleDTO): bool
    {
        return $this->assignmentsStorage->hasItem($roleDTO->getName());
    }

    /**
     * @throws NotExistItemException
     */
    public function isAssignedPermissionToUsers(PermissionDTO $permissionDTO): bool
    {
        return $this->assignmentsStorage->hasItem($permissionDTO->getName());
    }

    public function getUserAssignments(User $user): UserAssignmentsDTO
    {
        $rolesDTO = $this->getRolesByUser($user->getId());
        //getByUserId method is used instead of getPermissionsByUser, so as not to load inherited permissions
        $userAssignments = $this->assignmentsStorage->getByUserId((string)$user->getId());
        if(empty($rolesDTO) && empty($userAssignments)) {
            return new UserAssignmentsDTO($user);
        }

        $permissionsDTO = [];
        foreach ($userAssignments as $assignment) {
            $permission = $this->accessRightsService->getPermissionByName($assignment->getItemName());
            $permission === null  ?: $permissionsDTO[] = $permission;
        }

        return new UserAssignmentsDTO($user, $rolesDTO, $permissionsDTO);
    }

    public function getAssignments(): array
    {
        $assignments = $this->assignmentsStorage->getAll();

        $userIds = array_keys($assignments);
        /** @var User[] $users */
        $users = $this->userQueryService->getUsers($userIds);

        $usersDTO = [];
        foreach ($users as $user) {
            if (array_key_exists($user->getId(), $assignments)) {
                $userAssignments = $assignments[$user->getId()];
                $roles = [];
                $permissions = [];
                foreach ($userAssignments as $name => $assignment){
                    $role = $this->accessRightsService->getRoleByName($name);
                    $role === null ?: $roles[] = $role;

                    $permission = $this->accessRightsService->getPermissionByName($name);
                    $permission === null  ?: $permissions[] = $permission;
                }
                $usersDTO[$user->getId()] = new UserAssignmentsDTO($user, $roles, $permissions);
            }
        }

        return $usersDTO;
    }
}
