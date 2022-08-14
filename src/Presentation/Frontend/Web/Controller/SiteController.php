<?php

declare(strict_types=1);

namespace App\Presentation\Frontend\Web\Controller;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{

    public function __construct(private ViewRenderer $view)
    {
        $this->view = $view->withControllerName('controller/site');
    }

    public function index(): ResponseInterface
    {
        return $this->view->render('index');
    }
}
