<?php

declare(strict_types=1);

namespace App\IdentityAccess\Presentation\Frontend\Web\Auth;

use App\IdentityAccess\Presentation\Frontend\Web\Auth\Form\SignupForm;
use App\IdentityAccess\User\Application\Service\UserQueryServiceInterface;
use App\IdentityAccess\User\Application\Service\UserServiceInterface;
use App\IdentityAccess\User\Domain\Exception\IdentityException;
use App\Infrastructure\Authentication\AuthenticationService;
use App\Presentation\Infrastructure\Web\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SignupController
{

    public function __construct(
        private WebControllerService $webService,
        private ViewRenderer $view,
    ) {
        $this->view = $view->withViewPath('@identityView/auth/signup');
    }

    public function signup(
        AuthenticationService $authenticationService,
        UserServiceInterface $userService,
        UserQueryServiceInterface $userQueryService,
        ServerRequestInterface $request,
        TranslatorInterface $translator,
        ValidatorInterface $validator
    ): ResponseInterface {
        if (!$authenticationService->isGuest()) {
            return $this->webService->redirect('site/index');
        }

        $body = $request->getParsedBody();

        $signupForm = new SignupForm($userQueryService, $translator);

        if (
            $request->getMethod() === Method::POST
            && $signupForm->load(is_array($body) ? $body : [])
            && $validator->validate($signupForm)->isValid()
        ) {
            try {
                $userService->createUser($signupForm->getLogin(), $signupForm->getPassword());
                return $this->webService->sessionFlashAndRedirect(
                    $translator->translate('IdentityAccess.user.registered'),
                    'site/index'
                );
            } catch (IdentityException $exception) {
                $signupForm->getFormErrors()->addError('password', $exception->getMessage());
            }
        }

        return $this->view->render('signup', ['formModel' => $signupForm]);
    }
}
