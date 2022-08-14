<?php

declare(strict_types=1);

use App\Presentation\Backend\Web\Controller\SiteController;
use Yiisoft\Router\Route;

return [
    \Yiisoft\Router\Group::create('')
        ->routes(
            Route::get('/')
                ->action([SiteController::class, 'index'])
                ->name('index'),
        )
        ->host('backend.{_host}')
        ->namePrefix('backend/'),

    Route::get('/backend')
        ->action([SiteController::class, 'index'])
        ->name('index'),
];
