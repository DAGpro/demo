<?php

declare(strict_types=1);

namespace App\Core\Component\Blog\Application;

use App\Core\Component\Blog\Infrastructure\Persistence\Comment\CommentRepository;
use Yiisoft\Data\Paginator\KeysetPaginator;

final class CommentService
{
    private const COMMENTS_FEED_PER_PAGE = 10;

    public function __construct(private CommentRepository $repository)
    {
    }

    public function getFeedPaginator(): KeysetPaginator
    {
        return (new KeysetPaginator($this->repository->getReader()))
            ->withPageSize(self::COMMENTS_FEED_PER_PAGE);
    }
}
