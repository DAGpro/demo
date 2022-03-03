<?php

declare(strict_types=1);

namespace App\Blog\Presentation\Frontend\Web\Post;

use App\Blog\Application\Service\QueryService\ReadPostQueryServiceInterface;
use App\Blog\Infrastructure\Services\IdentityAccessService;
use App\Presentation\Infrastructure\Web\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\ViewRenderer;

final class PostController
{

    public function __construct(
        private WebControllerService $webService,
        private IdentityAccessService $identityAccessService,
        private ViewRenderer $view
    ) {
        $this->view = $view->withViewPath('@blogView/post');
    }

    public function index(
        CurrentRoute $currentRoute,
        ReadPostQueryServiceInterface $postQueryService
    ): Response {
        $slug = $currentRoute->getArgument('slug', '');

        if (($item = $postQueryService->fullPostPage($slug)) === null) {
            return $this->webService->notFound();
        }

        $canEdit = $this->identityAccessService->isAuthor($item);
        $commentator = $this->identityAccessService->getCommentator();

        return $this->view->render('index', [
            'item' => $item,
            'canEdit' => $canEdit,
            'commentator' => $commentator,
            'slug' => $slug
        ]);
    }

    public function findAuthorPosts(Request $request, ReadPostQueryServiceInterface $postQueryService): Response
    {
        $authorName = $request->getAttribute('author', '');
        $author = $this->identityAccessService->findAuthor($authorName);
        if ($author === null) {
            return $this->webService->notFound();
        }

        $data = $postQueryService->findByAuthor($author);

        $currentAuthor = $this->identityAccessService->getAuthor();
        $canEdit = $currentAuthor !== null && $author->isEqual($currentAuthor);

        return $this->view->render('author-posts', ['dataReader' => $data, 'canEdit' => $canEdit]);
    }

}
