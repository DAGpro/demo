<?php

declare(strict_types=1);

namespace App\Presentation\Backend\Web\Controller;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{

    public function __construct(private ViewRenderer $view)
    {
        $this->view = $view
            ->withController($this)
            ->withLayout('@backendLayout/main')
            ->withViewPath('@backendView/controller');
    }

    public function index(): ResponseInterface
    {
        return $this->view->render('index');
    }
}
