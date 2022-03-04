<?php

declare(strict_types=1);

namespace App\IdentityAccess\Presentation\Backend\Web\Access;

use App\IdentityAccess\Access\Application\Service\AccessRightsServiceInterface;
use App\Infrastructure\Presentation\Web\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;

final class AccessRightsController
{

    public function __construct(
        private WebControllerService $webService,
        private AccessRightsServiceInterface $accessRightsService,
        private ViewRenderer $view,
    ) {
        $view = $view->withLayout('@backendLayout/main');
        $view = $view->withViewPath('@identityBackendView/access');
        $this->view = $view->withControllerName('access-rights');
    }

    public function index(): ResponseInterface
    {
        $rolesWithChildren = $this->accessRightsService->getRoles();

        return $this->view->render('index', [
            'roles' => $rolesWithChildren,
            'currentUrl' => 'roles',
        ]);
    }

    public function permissionsList(): ResponseInterface
    {
        $permissions = $this->accessRightsService->getPermissions();

        return $this->view->render('permissions-list', [
            'permissions' => $permissions,
            'currentUrl' => 'permissions',
        ]);
    }

    public function viewRole(CurrentRoute $currentRoute): ResponseInterface
    {
        $roleName = $currentRoute->getArgument('role_name');
        if (null === $roleName){
            return $this->webService->sessionFlashAndRedirect(
                'The request role name arguments are required!',
                'backend/access/assignments',
                [],
                'danger'
            );
        }

        $role = $this->accessRightsService->getRoleByName($roleName);
        if (null === $role){
            return $this->webService->notFound();
        }

        return $this->view->render('view-role', [
            'role' => $role,
            'currentUrl' => null,
        ]);
    }
}
