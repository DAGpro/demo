<?php

declare(strict_types=1);

namespace App\IdentityAccess\Presentation\Frontend\Web\User;

use App\IdentityAccess\User\Application\Service\UserServiceInterface;
use App\IdentityAccess\User\Domain\Exception\IdentityException;
use App\Infrastructure\Authentication\AuthenticationService;
use App\Infrastructure\Presentation\Web\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Yii\View\ViewRenderer;

final class CabinetController
{

    public function __construct(
        private ViewRenderer $view,
        private WebControllerService $webService,
        private AuthenticationService $authenticationService
    ) {
        $this->view = $view->withViewPath('@identityView/user/cabinet');
    }

    public function index(): Response
    {
        $user = $this->authenticationService->getUser();

        return $this->view->render('index', ['item' => $user]);
    }

    public function deleteAccount(
        UserServiceInterface $userService
    ): Response {
        try {
            $user = $this->authenticationService->getUser();
            if ($user === null) {
                return $this->webService->accessDenied();
            }

            $userService->deleteUser($user->getId());

            return $this->webService->redirect('site/index');
        } catch (IdentityException $e){
            return $this->webService->notFound();
        }

    }
}
