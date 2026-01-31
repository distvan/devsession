<?php

declare(strict_types=1);

namespace DevSession\Command;

use DevSession\Git\GitServiceInterface;
use DevSession\Session\Session;
use DevSession\Session\SessionRepositoryInterface;
use DateTimeImmutable;
use DevSession\Util\Console;
use RuntimeException;
use InvalidArgumentException;

final class StartCommand implements CommandInterface
{
    public function __construct(
        private SessionRepositoryInterface $repository,
        private GitServiceInterface $git,
        private array $arguments = []
    ) {}

    public function execute(): void
    {
        $title = $this->arguments[0] ?? null;

        if ($title === null) {
            throw new InvalidArgumentException('Session title is required.');
        }

        if ($this->repository->hasActiveSession()) {
            throw new RuntimeException('A session is already active.');
        }
        
        $session = new Session(
            id: bin2hex(random_bytes(8)),
            title: $title,
            startedAt: new DateTimeImmutable(),
            gitRepo: $this->git->getRepositoryName(),
            gitBranch: $this->git->getCurrentBranch()
        );

        $this->repository->start($session);
        Console::success('Session started');
        Console::line('Title: ' . $session->title);

        if($session->gitBranch !== null) {
            Console::line('Branch: ' . $session->gitBranch);
        }
    }
}