<?php

declare(strict_types=1);

namespace App\Presentation\Backend\Console\Command\Fixture;

use App\Core\Component\Blog\Domain\PostTag;
use App\Core\Component\Blog\Domain\Tag;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyProxy;
use App\Core\Component\Blog\Domain\Comment;
use App\Core\Component\Blog\Domain\Post;
use App\Core\Component\User\Domain\User;

final class SchemaClearCommand extends Command
{
    protected static $defaultName = 'fixture/schema/clear';

    public function __construct(
        private CycleDependencyProxy $promise,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Clear database from fixtures')
            ->setHelp('This command delete all tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('post')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('post_tag')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('tag')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('user')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('comment')
            ->run();

        return 0 === $this->promise
                ->getORM()
                ->getRepository(Post::class)
                ->select()
                ->count() +
            $this->promise
                ->getORM()
                ->getRepository(PostTag::class)
                ->select()
                ->count() +
            $this->promise
                ->getORM()
                ->getRepository(Tag::class)
                ->select()
                ->count() +
            $this->promise
                ->getORM()
                ->getRepository(User::class)
                ->select()
                ->count() +
            $this->promise
                ->getORM()
                ->getRepository(Comment::class)
                ->select()
                ->count()
            ? ExitCode::OK
            : ExitCode::UNSPECIFIED_ERROR;
    }
}
