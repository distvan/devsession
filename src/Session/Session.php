<?php

declare(strict_types=1);

namespace DevSession\Session;

use DateTimeImmutable;

final class Session
{
    public function __construct(
        public string $id,
        public string $title,
        public DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $endedAt = null,
        public ?string $gitRepo = null,
        public ?string $gitBranch = null,
        public array $commits = [],
        public array $filesChanges = []
    ) {}
}
