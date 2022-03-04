<?php

declare(strict_types=1);

namespace App\Infrastructure\Presentation\Web\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Yii\View\ViewRenderer;

final class NotFoundHandler implements RequestHandlerInterface
{

    public function __construct(private ViewRenderer $view)
    {
        $this->view = $view->withControllerName('controller/site');
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->view
            ->render('404')
            ->withStatus(Status::NOT_FOUND);
    }
}
