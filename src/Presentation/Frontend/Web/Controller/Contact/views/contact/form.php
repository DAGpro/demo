<?php

declare(strict_types=1);

use Yiisoft\Form\Field;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var Yiisoft\Yii\View\Csrf $csrf
 * @var \App\Presentation\Frontend\Web\Controller\Contact\ContactForm $form
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var \Yiisoft\Form\Field $field
 * @var WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 */

$this->setTitle($translator->translate('menu.contact'));
?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-8">
            <div class="card border border-dark shadow-2-strong rounded-3">
                <div class="card-header bg-dark text-white">
                    <h1 class="fw-normal h3 text-center"><?= Html::encode($this->getTitle()) ?></h1>
                </div>
                <div class="card-body p-5 text-center">
                    <?= Form::tag()
                        ->post($url->generate('site/contact'))
                        ->enctypeMultipartFormData()
                        ->csrf($csrf)
                        ->id('form-contact')
                        ->open()
                    ?>

                    <?= Field::text($form, 'name') ?>
                    <?= Field::email($form, 'email') ?>
                    <?= Field::text($form, 'subject') ?>
                    <?= Field::textarea($form, 'body')->addInputAttributes(['style' => 'height: 100px']) ?>
                    <?= Field::file($form, 'attachFiles[]')
                        ->containerClass('mb-3')
                        ->multiple()
                        ->label($translator->translate('form.attach-files'))
                    ?>
                    <?= Field::buttonGroup()
                        ->buttons(
                            Button::reset($translator->translate('button.reset'))->addClass('btn btn-md btn-danger'),
                            Button::submit($translator->translate('button.submit'))->addClass('btn btn-md btn-primary')
                        )
                        ->containerClass('btn-group btn-toolbar float-end')

                    ?>

                    <?= Form::tag()->close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
