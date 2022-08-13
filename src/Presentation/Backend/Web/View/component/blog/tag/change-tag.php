<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var Field $field
 * @var \Yiisoft\Translator\Translator $translator
 * @var \App\Presentation\Backend\Web\Component\Blog\Form\TagForm $form
 * @var string $csrf
 * @var array $action
 * @var string $title
 * @var \App\Blog\Domain\Tag $tag
 * @var array $error
 */

use Yiisoft\Form\Field;
use Yiisoft\Html\Tag\Form;

$this->setTitle($title);

?>
    <div class="main row">
        <div class="col-md-5">
            <h3> <?=$translator->translate('blog.tag.change') . $form->getLabel()?></h3>
            <?= Form::tag()
                ->post($url->generate(...$action))
                ->addAttributes(['enctype' => 'multipart/form-data'])
                ->csrf($csrf)
                ->id('form-moderate-tag')
                ->open() ?>

            <?= Field::text($form, 'label') ?>
            <?= Field::text($form, 'id')->addInputAttributes(['disabled' => 'disabled']) ?>

            <?= Field::submitButton($translator->translate('button.submit'))
                ->addButtonAttributes(
                    [
                        'class' => 'btn btn-primary btn-lg mt-3',
                        'id' => 'login-button'
                    ]
                )
            ?>

            <?= Form::tag()->close()?>
        </div>
    </div>
<?php
