<?php

declare(strict_types=1);

namespace App\Presentation\Frontend\Web\Component\Auth;

use App\Core\Component\Auth\AuthService;
use App\Presentation\Frontend\Web\Component\Auth\Form\LoginForm;
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
        private AuthService $authService,
        private WebControllerService $webService,
        private ViewRenderer $viewRenderer,
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('component/auth');
    }

    public function login(
        ServerRequestInterface $request,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        CookieLogin $cookieLogin
    ): ResponseInterface {
        if (!$this->authService->isGuest()) {
            return $this->redirectToMain();
        }

        $body = $request->getParsedBody();
        $loginForm = new LoginForm($this->authService, $translator);

        if (
            $request->getMethod() === Method::POST
            && $loginForm->load(is_array($body) ? $body : [])
            && $validator
                ->validate($loginForm)
                ->isValid()
        ) {
            $identity = $this->authService->getIdentity();

            if ($identity instanceof CookieLoginIdentityInterface && $loginForm->getAttributeValue('rememberMe')) {
                return $cookieLogin->addCookie($identity, $this->redirectToMain());
            }

            return $this->redirectToMain();
        }

        return $this->viewRenderer->render('login', ['formModel' => $loginForm]);
    }

    public function logout(): ResponseInterface
    {
        $this->authService->logout();

        return $this->redirectToMain();
    }

    private function redirectToMain(): ResponseInterface
    {
        return $this->webService->getRedirectResponse('site/index');
    }
}
