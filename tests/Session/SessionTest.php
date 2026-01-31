<?php

declare(strict_types=1);

namespace DevSession\Tests\Session;

use DevSession\Session\Session;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testSessionCanBeCreated(): void
    {
        $startedAt = new DateTimeImmutable();
        $session = new Session(
            id: 'test123',
            title: 'Test Session',
            startedAt: $startedAt
        );
        
        $this->assertSame('test123', $session->id);
        $this->assertSame('Test Session', $session->title);
        $this->assertInstanceOf(DateTimeImmutable::class, $session->startedAt);
    }

    public function testSessionDefaultValuesAreNull(): void
    {
        $session = new Session(
            id: 'test',
            title: 'Test',
            startedAt: new DateTimeImmutable()
        );
        
        $this->assertNull($session->endedAt);
        $this->assertNull($session->gitRepo);
        $this->assertNull($session->gitBranch);
    }

    public function testSessionArraysDefaultToEmpty(): void
    {
        $session = new Session(
            id: 'test',
            title: 'Test',
            startedAt: new DateTimeImmutable()
        );
        
        $this->assertIsArray($session->commits);
        $this->assertEmpty($session->commits);
        $this->assertIsArray($session->filesChanges);
        $this->assertEmpty($session->filesChanges);
    }

    public function testSessionCanHaveEndedAt(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $endedAt = new DateTimeImmutable('2026-01-25 11:00:00');
        
        $session = new Session(
            id: 'test',
            title: 'Test',
            startedAt: $startedAt,
            endedAt: $endedAt
        );
        
        $this->assertInstanceOf(DateTimeImmutable::class, $session->endedAt);
        $this->assertGreaterThan($session->startedAt, $session->endedAt);
    }

    public function testSessionCanHaveGitInformation(): void
    {
        $session = new Session(
            id: 'test',
            title: 'Test',
            startedAt: new DateTimeImmutable(),
            gitRepo: 'my-repo',
            gitBranch: 'feature/test'
        );
        
        $this->assertSame('my-repo', $session->gitRepo);
        $this->assertSame('feature/test', $session->gitBranch);
    }

    public function testSessionCanStoreCommitsAndFileChanges(): void
    {
        $session = new Session(
            id: 'test',
            title: 'Test',
            startedAt: new DateTimeImmutable(),
            commits: ['commit1', 'commit2'],
            filesChanges: ['file1.php', 'file2.php']
        );
        
        $this->assertCount(2, $session->commits);
        $this->assertCount(2, $session->filesChanges);
        $this->assertContains('commit1', $session->commits);
        $this->assertContains('file1.php', $session->filesChanges);
    }
}
