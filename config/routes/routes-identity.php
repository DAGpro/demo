<?php

declare(strict_types=1);

use App\Presentation\Frontend\Api\Component\IdentityAccess\User\ApiUserController;
use App\Presentation\Frontend\Api\Controller\Actions\ApiInfo;
use App\Presentation\Frontend\Web\Component\IdentityAccess\Auth\AuthController;
use App\Presentation\Frontend\Web\Component\IdentityAccess\Auth\SignupController;
use App\Presentation\Frontend\Web\Component\IdentityAccess\User\CabinetController;
use App\Presentation\Frontend\Web\Component\IdentityAccess\User\UserController;
use App\Presentation\Infrastructure\Api\Middleware\ApiDataWrapper;
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Yii\RateLimiter\Counter;
use Yiisoft\Yii\RateLimiter\LimitRequestsMiddleware;
use Yiisoft\Yii\RateLimiter\Storage\StorageInterface;

return [
    Route::methods([Method::GET, Method::POST], '/login')
        ->name('auth/login')
        ->middleware(LimitRequestsMiddleware::class)
        ->action([AuthController::class, 'login']),

    Route::post('/logout')
        ->name('auth/logout')
        ->action([AuthController::class, 'logout']),

    Route::methods([Method::GET, Method::POST], '/signup')
        ->name('auth/signup')
        ->middleware(fn(
            ResponseFactoryInterface $responseFactory,
            StorageInterface $storage
        ) => new LimitRequestsMiddleware(new Counter($storage, 5, 5), $responseFactory))
        ->action([SignupController::class, 'signup']),

    // Identity routes
    Group::create('/user')
        ->routes(
            Route::get( '/cabinet')
                ->name('user/cabinet')
                ->middleware(Authentication::class)
                ->action([CabinetController::class, 'index']),

            Route::post( 'cabinet/delete')
                ->name('user/cabinet/delete')
                ->middleware(Authentication::class)
                ->action([CabinetController::class, 'deleteAccount']),

            Route::get( '/all[/page-{page}]')
                ->name('user/index')
                ->action([UserController::class, 'index']),

            Route::get( '/profile/{login}')
                ->name('user/profile')
                ->action([UserController::class, 'profile']),
        ),

    // API group.
    // By default it responds with XML regardless of content-type.
    // Individual sub-routes are responding with JSON.
    Group::create('/api')
        ->middleware(FormatDataResponseAsXml::class)
        ->middleware(ApiDataWrapper::class)
        ->routes(
            Route::get('/info/v1')
                ->name('api/info/v1')
                ->action(function (DataResponseFactoryInterface $responseFactory) {
                    return $responseFactory->createResponse(['version' => '1.0', 'author' => 'yiisoft']);
                }),
            Route::get('/info/v2')
                ->name('api/info/v2')
                ->middleware(FormatDataResponseAsJson::class)
                ->action(ApiInfo::class),
            Route::get('/user')
                ->name('api/user/index')
                ->action([ApiUserController::class, 'index']),
            Route::get('/user/{login}')
                ->name('api/user/profile')
                ->middleware(FormatDataResponseAsJson::class)
                ->action([ApiUserController::class, 'profile'])
        ),
];
