<?php

declare(strict_types=1);

namespace App\Presentation\Backend\Console\Component\IdentityAccess\Access\Management;

use App\IdentityAccess\Access\Application\Service\AccessManagementServiceInterface;
use App\IdentityAccess\Access\Application\Service\PermissionDTO;
use App\IdentityAccess\Access\Domain\Exception\ExistItemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;

final class AddPermissionCommand extends Command
{
    protected static $defaultName = 'access/addPermission';

    public function __construct(private AccessManagementServiceInterface $accessManagementService)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Add permission from access control rights')
            ->setHelp('This command allows you to add permission from access control rights')
            ->addArgument('permission', InputArgument::REQUIRED, 'RBAC permission');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $permissionName = $input->getArgument('permission');

        try {
            $permissionDTO = new PermissionDTO($permissionName);
            $this->accessManagementService->addPermission($permissionDTO);

            $io->success(
                sprintf(
                    '`%s` access permission has been created!!',
                    $permissionDTO->getName(),
                )
            );

            return ExitCode::OK;
        } catch (ExistItemException $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
