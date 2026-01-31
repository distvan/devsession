<?php

declare(strict_types=1);

namespace DevSession\Git;

use DateTimeImmutable;

interface GitServiceInterface
{
    public function getRepositoryName(): ?string;
    public function getCurrentBranch(): ?string;
    public function getCommitsSince(DateTimeImmutable $since): array;
    public function getChangedFilesSince(DateTimeImmutable $since): array;
}
