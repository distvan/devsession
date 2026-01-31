<?php

declare(strict_types=1);

namespace DevSession\Command;

use DevSession\Session\SessionRepositoryInterface;
use DevSession\Git\GitServiceInterface;
use DevSession\Util\Console;
use DateTimeImmutable;
use DevSession\Util\TimeFormatter;
use RuntimeException;

final class EndCommand implements CommandInterface
{
    public function __construct(
        private SessionRepositoryInterface $repository,
        private GitServiceInterface $git
    ) {}

    public function execute(): void
    {
        $session = $this->repository->getActiveSession();
        if ($session === null) {
            throw new RuntimeException('No active session to end.');
        }
        
        $endedAt = new DateTimeImmutable();
        $commits = $this->git->getCommitsSince($session->startedAt);
        $files = $this->git->getChangedFilesSince($session->startedAt);
        
        $session->endedAt = $endedAt;
        $session->commits = $commits;
        $session->filesChanges = $files;

        $this->repository->end($session);

        $duration = $session->endedAt->getTimestamp() - $session->startedAt->getTimestamp();

        Console::success('Session ended');
        Console::line('');
        Console::line('Title: ' . $session->title);
        Console::line('Duration: ' . TimeFormatter::seconds($duration));
        Console::line('Commits: ' . count($commits));

        if(!empty($files)) {
            Console::line('Changed files:');
            foreach ($files as $file) {
                Console::line(' - ' . $file);
            }
        }
    }
}
