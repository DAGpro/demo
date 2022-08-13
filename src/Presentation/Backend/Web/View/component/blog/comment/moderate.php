<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var Field $field
 * @var \App\Presentation\Backend\Web\Component\Blog\Form\CommentForm $form
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \App\Blog\Domain\Comment $comment
 * @var string $csrf
 * @var array $action
 * @var string $title
 */

use Yiisoft\Form\Field;
use Yiisoft\Html\Tag\Form;

$this->setTitle($translator->translate('blog.moderate.comment') . $form->getCommentId());

?>
<div class="main">
    <h1><?= $this->getTitle()?></h1>
    <?= Form::tag()
        ->post($url->generate(...$action))
        ->addAttributes(['enctype' => 'multipart/form-data'])
        ->csrf($csrf)
        ->id('form-moderate-comment')
        ->open() ?>

    <?= Field::textArea($form, 'content')->addInputAttributes(['rows' => '9', 'style' => 'height: 250px;']) ?>
    <?= Field::checkbox($form, 'public')
        ->inputValue(true)
        ->addInputAttributes(['class' => 'form-check-input'])
        ->containerAttributes(['class' => 'form-check'])
    ?>
    <?= Field::number($form, 'comment_id')->addInputAttributes(['disabled' => 'disabled']) ?>

    <?= Field::submitButton($translator->translate('button.submit'))
        ->addButtonAttributes(
            [
                'class' => 'btn btn-primary btn-lg mt-3',
                'id' => 'login-button',
            ]
        )
    ?>
    <?=Form::tag()->close()?>
</div>
