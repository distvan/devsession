<?php

declare(strict_types=1);

namespace DevSession\Session;

use DateTimeImmutable;

interface SessionRepositoryInterface
{
    public function hasActiveSession(): bool;
    public function start(Session $session): void;
    public function end(Session $session): void;
    public function getCompletedSessions(?int $limit = null): array;
    public function getSessionsForDate(DateTimeImmutable $date): array;
    public function getActiveSession(): ?Session;
}
