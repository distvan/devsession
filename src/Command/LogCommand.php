<?php

declare(strict_types=1);

namespace DevSession\Command;

use DevSession\Util\Console;
use DevSession\Session\SessionRepositoryInterface;
use DevSession\Util\TimeFormatter;

final class LogCommand implements CommandInterface
{
    public function __construct(
        private SessionRepositoryInterface $repository,
        private array $arguments = []
    ) {}

    public function execute(): void
    {
        $limit = $this->getOption('last') ?? 10;
        $sessions = $this->repository->getCompletedSessions($limit);

        if (empty($sessions)) {
            Console::line('No sessions recorded.');
        }

        Console::line('Session Log');
        Console::line('');

        foreach ($sessions as $session) {
            $duration = $session->endedAt->getTimestamp() - $session->startedAt->getTimestamp();
            
            Console::line('- ' . $session->title);
            Console::line('  Date: ' . $session->endedAt->format('Y-m-d H:i'));
            Console::line('  Duration: ' . TimeFormatter::seconds($duration));
            Console::line('  Commits: ' . count($session->commits));
            Console::line('');
        }
    }

    private function getOption(string $name): ?int
    {
        foreach ($this->arguments as $index => $argument) {
            if ($argument === "--{$name}" && isset($this->arguments[$index + 1])) {
                return (int)$this->arguments[$index + 1];
            }
        }

        return null;
    }
}
