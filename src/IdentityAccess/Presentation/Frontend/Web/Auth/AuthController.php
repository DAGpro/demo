<?php

declare(strict_types=1);

namespace App\IdentityAccess\Presentation\Frontend\Web\Auth;

use App\IdentityAccess\Presentation\Frontend\Web\Auth\Form\LoginForm;
use App\IdentityAccess\User\Application\Service\UserQueryServiceInterface;
use App\Infrastructure\Authentication\AuthenticationException;
use App\Infrastructure\Authentication\AuthenticationService;
use App\Presentation\Infrastructure\Web\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\Login\Cookie\CookieLogin;
use Yiisoft\User\Login\Cookie\CookieLoginIdentityInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class AuthController
{
    public function __construct(
        private ViewRenderer $view,
        private AuthenticationService $authService,
        private WebControllerService $webService
    ) {
        $this->view = $view->withViewPath('@identityView/auth/auth');
    }

    public function login(
        ServerRequestInterface $request,
        UserQueryServiceInterface $userQueryService,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        CookieLogin $cookieLogin
    ): ResponseInterface {
        if (!$this->authService->isGuest()) {
            return $this->redirectToMain();
        }

        $body = $request->getParsedBody();
        $loginForm = new LoginForm($userQueryService, $translator);

        if (
            $request->getMethod() === Method::POST
            && $loginForm->load(is_array($body) ? $body : [])
            && $validator
                ->validate($loginForm)
                ->isValid()
        ) {
            try {
                $identity = $this->authService->login($loginForm->getLogin(), $loginForm->getPassword());
                if ($identity instanceof CookieLoginIdentityInterface && !$loginForm->getAttributeValue('rememberMe')) {
                    return $cookieLogin->addCookie($identity, $this->redirectToMain());
                }

                return $this->redirectToMain();
            } catch (AuthenticationException $exception) {
                $loginForm->getFormErrors()->addError('password', $translator->translate($exception->getMessage()));
            }

        }

        return $this->view->render('login', ['formModel' => $loginForm]);
    }

    public function logout(): ResponseInterface
    {
        $this->authService->logout();

        return $this->redirectToMain();
    }

    private function redirectToMain(): ResponseInterface
    {
        return $this->webService->redirect('site/index');
    }
}
