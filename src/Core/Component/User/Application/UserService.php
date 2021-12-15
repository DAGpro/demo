<?php

declare(strict_types=1);

namespace App\Core\Component\User\Application;

use App\Core\Component\User\Domain\User;
use App\Core\Component\User\Infrastructure\Persistence\UserRepository;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\User\CurrentUser;

final class UserService
{
    public function __construct(
        private CurrentUser $currentUser,
        private UserRepository $repository,
        private AccessCheckerInterface $accessChecker
    ) {
    }

    public function getUser(): ?User
    {
        $userId = $this->currentUser->getId();

        if ($userId === null) {
            return null;
        }

        return $this->repository->findById($this->currentUser->getId());
    }

    public function hasPermission(string $permission): bool
    {
        $userId = $this->currentUser->getId();
        return null !== $userId && $this->accessChecker->userHasPermission($userId, $permission);
    }
}
