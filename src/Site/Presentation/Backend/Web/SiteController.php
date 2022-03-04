<?php

declare(strict_types=1);

namespace App\Site\Presentation\Backend\Web;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{

    public function __construct(private ViewRenderer $view)
    {
        $this->view = $view
            ->withController($this)
            ->withLayout('@backendLayout/main')
            ->withViewPath('@backendView');
    }

    public function index(): ResponseInterface
    {
        return $this->view->render('index');
    }
}
