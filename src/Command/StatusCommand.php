<?php
declare(strict_types=1);

namespace DevSession\Command;

use DevSession\Util\Console;
use DevSession\Util\TimeFormatter;
use DevSession\Session\SessionRepositoryInterface;
use DateTimeImmutable;

final class StatusCommand implements CommandInterface
{
    public function __construct(
        private SessionRepositoryInterface $repository
    ) {}

    public function execute(): void
    {
        $session = $this->repository->getActiveSession();

        if ($session === null) {
            Console::line('No active session.');
            return;
        }

        $now = new DateTimeImmutable();
        $duration = $now->getTimestamp() - $session->startedAt->getTimestamp();

        Console::line('Active session');
        Console::line('');
        Console::line('Title: ' . $session->title);
        Console::line('Started: ' . $session->startedAt->format('H:i'));
        Console::line('Elapsed: ' . TimeFormatter::seconds($duration));

        if ($session->gitBranch !== null) {
            Console::line('Branch: ' . $session->gitBranch);
        }
    }
}
