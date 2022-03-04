<?php

declare(strict_types=1);

namespace App\Site\Presentation\Frontend\Web;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{

    public function __construct(private ViewRenderer $view)
    {
        $this->view = $view->withControllerName('site');
    }

    public function index(): ResponseInterface
    {
        return $this->view->render('index');
    }
}
