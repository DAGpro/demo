<?php

declare(strict_types=1);

namespace App\IdentityAccess\Presentation\Frontend\Web\Auth\Form;

use App\IdentityAccess\User\Application\Service\UserQueryServiceInterface;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    private string $login = '';
    private string $password = '';
    private bool $rememberMe = false;

    public function __construct(
        private UserQueryServiceInterface $userService,
        private TranslatorInterface $translator
    ) {
        parent::__construct();
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAttributeLabels(): array
    {
        return [
            'login' => $this->translator->translate('identityAccess.form.login'),
            'password' => $this->translator->translate('identityAccess.form.password'),
            'rememberMe' => $this->translator->translate('identityAccess.form.remember'),
        ];
    }

    public function getFormName(): string
    {
        return 'Login';
    }

    public function getRules(): array
    {
        return [
            'login' => [new Required()],
            'password' => $this->passwordRules(),
        ];
    }

    private function passwordRules(): array
    {
        return [
            new Required(),
            new Callback(
                callback: function (): Result {
                    $result = new Result();

                $user = $this->userService->findByLogin($this->login);

                if ($user === null || !$user->validatePassword($this->password)) {
                    $this->getFormErrors()->addError('login', '');
                    $result->addError($this->translator->translate('validator.invalid.login.password'));
                }

                    return $result;
                },
                skipOnEmpty: true,
            ),
        ];
    }
}
