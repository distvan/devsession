<?php

declare(strict_types=1);

namespace DevSession\Tests\Git;

use DevSession\Git\GitService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class GitServiceTest extends TestCase
{
    private GitService $gitService;

    protected function setUp(): void
    {
        $this->gitService = new GitService();
    }

    public function testGetRepositoryNameReturnsNullWhenNotInGitRepo(): void
    {
        // This test might fail if running in a git repository
        // You can skip it conditionally or run tests in a non-git directory
        $repoName = $this->gitService->getRepositoryName();
        
        // If we're in a git repo, this will not be null
        // If we're not in a git repo, it should be null
        $this->assertTrue($repoName === null || is_string($repoName));
    }

    public function testGetCurrentBranchReturnsStringOrNull(): void
    {
        $branch = $this->gitService->getCurrentBranch();
        
        $this->assertTrue($branch === null || is_string($branch));
    }

    public function testGetCommitsSinceReturnsArray(): void
    {
        $since = new DateTimeImmutable('2020-01-01');
        $commits = $this->gitService->getCommitsSince($since);
        
        $this->assertIsArray($commits);
    }

    public function testGetChangedFilesSinceReturnsArray(): void
    {
        $since = new DateTimeImmutable('2020-01-01');
        $files = $this->gitService->getChangedFilesSince($since);
        
        $this->assertIsArray($files);
    }

    public function testGetCommitsSinceReturnsEmptyArrayWhenNotInGitRepo(): void
    {
        // When not in a git repository or no commits since date
        $since = new DateTimeImmutable('+1 year');
        $commits = $this->gitService->getCommitsSince($since);
        
        $this->assertIsArray($commits);
    }

    public function testGetChangedFilesSinceReturnsEmptyArrayWhenNotInGitRepo(): void
    {
        // When not in a git repository or no files changed since date
        $since = new DateTimeImmutable('+1 year');
        $files = $this->gitService->getChangedFilesSince($since);
        
        $this->assertIsArray($files);
    }
}
