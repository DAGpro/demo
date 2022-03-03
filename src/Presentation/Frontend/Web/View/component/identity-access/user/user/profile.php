<?php

declare(strict_types=1);

/**
 * @var \App\IdentityAccess\User\Domain\User $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

$this->setTitle($item->getLogin());

echo Html::tag('h1', Html::encode($this->getTitle()));
?>
<div>
    <span class="text-muted">Created at <?= $item->getCreatedAt()->format('H:i:s d.m.Y') ?></span>
</div>
