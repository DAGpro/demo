<?php

declare(strict_types=1);

use Yiisoft\Form\FormModelInterface;
use Yiisoft\Form\Field;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var string $csrf
 * @var FormModelInterface $formModel
 */

$this->setTitle($translator->translate('Signup'));
?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card border border-dark shadow-2-strong rounded-3">
                <div class="card-header bg-dark text-white">
                    <h1 class="fw-normal h3 text-center"><?= Html::encode($this->getTitle()) ?></h1>
                </div>
                <div class="card-body p-5 text-center">)
                    <?= Form::tag()
                        ->post($url->generate('auth/signup'))
                        ->csrf($csrf)
                        ->id('signupForm')
                        ->open() ?>

                    <?= Field::text($formModel, 'login')->autofocus() ?>
                    <?= Field::password($formModel, 'password') ?>
                    <?= Field::password($formModel, 'passwordVerify') ?>
                    <?= Field::submitButton($translator->translate('button.submit'))
                        ->buttonId('register-button')
                        ->name('register-button')
                    ?>

                    <?= Form::tag()->close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
