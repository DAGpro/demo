<?php

declare(strict_types=1);

namespace App\IdentityAccess\Presentation\Backend\Web\Access;

use App\IdentityAccess\Access\Application\Service\AssignmentsServiceInterface;
use App\IdentityAccess\User\Application\Service\UserQueryServiceInterface;
use App\IdentityAccess\User\Domain\Exception\IdentityException;
use App\Infrastructure\Presentation\Web\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;

final class AssignmentsController
{
    public function __construct(
        private WebControllerService $webService,
        private UserQueryServiceInterface $userQueryService,
        private AssignmentsServiceInterface $assignmentsService,
        private ViewRenderer $view,
    ) {
        $view = $view->withLayout('@backendLayout/main');
        $view = $view->withViewPath('@identityBackendView/access');
        $this->view = $view->withControllerName('assignments');
    }

    public function assignments(): ResponseInterface
    {
        $usersAssignments = $this->assignmentsService->getAssignments();

        return $this->view->render('assignments', [
            'users' => $usersAssignments,
            'currentUrl' => 'assignments'
        ]);

    }

    public function userAssignments(Request $request, CurrentRoute $currentRoute): ResponseInterface
    {
        $userId = $currentRoute->getArgument('user_id');
        if ($userId === null) {
            return $this->webService->sessionFlashAndRedirect(
                'The request must have a user_id argument',
                'backend/access/assignments',
                [], 'danger'
            );
        }

        try {
            $user = $this->userQueryService->getUser((int)$userId);
            if ($user === null) {
                throw new IdentityException('User is not found!');
            }

            $userWithAssignments = $this->assignmentsService->getUserAssignments($user);

            return $this->view->render('user-assignments', [
                'user' => $userWithAssignments,
                'currentUrl' => null,
            ]);
        } catch (IdentityException $exception) {
            return $this->webService->notFound();
        }
    }
}
