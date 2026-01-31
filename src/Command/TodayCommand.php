<?php

declare(strict_types=1);

namespace DevSession\Command;

use DevSession\Session\SessionRepositoryInterface;
use DevSession\Util\Console;
use DateTimeImmutable;
use DevSession\Util\TimeFormatter;

final class TodayCommand implements CommandInterface
{
    public function __construct(
        private SessionRepositoryInterface $repository
    ) {}

    public function execute(): void
    {
        $sessions = $this->repository->getSessionsForDate(new DateTimeImmutable());
        if (empty($sessions)) {
            Console::line('No sessions recorded today.');
            return;
        }
        Console::line('Today (' . count($sessions) . ' session' . (count($sessions) === 1 ? '' : 's') . '):');
        $totalSeconds = 0;
        foreach ($sessions as $session) {
            $duration = $session->endedAt->getTimestamp() - $session->startedAt->getTimestamp();
            $totalSeconds += $duration;
            Console::line('- ' . $session->title . ' (' .  TimeFormatter::seconds($duration) . ')');
        }
        Console::line('');
        Console::line('Total focused time: ' . TimeFormatter::seconds($totalSeconds));
    }
}
