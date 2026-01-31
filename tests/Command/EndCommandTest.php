<?php

declare(strict_types=1);

namespace DevSession\Tests\Command;

use DevSession\Command\EndCommand;
use DevSession\Git\GitServiceInterface;
use DevSession\Session\Session;
use DevSession\Session\SessionRepositoryInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use DateTimeImmutable;

final class EndCommandTest extends TestCase
{
    public function testExecuteThrowsExceptionWhenNoActiveSession(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No active session to end.');
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn(null);
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        
        $command = new EndCommand($mockRepo, $mockGit);
        $command->execute();
    }

    public function testExecuteEndsSessionSuccessfully(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Test Session',
            startedAt: $startedAt
        );
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        $mockRepo->expects($this->once())
            ->method('end')
            ->with($this->callback(function ($s) use ($session) {
                return $s->id === $session->id && $s->endedAt !== null;
            }));
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->method('getCommitsSince')->willReturn([]);
        $mockGit->method('getChangedFilesSince')->willReturn([]);
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Session ended', $output);
        $this->assertStringContainsString('Test Session', $output);
    }

    public function testExecuteCapturesGitCommits(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Feature Work',
            startedAt: $startedAt
        );
        
        $commits = [
            'abc123 - Add login feature',
            'def456 - Fix validation bug',
            'ghi789 - Update tests'
        ];
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        $mockRepo->expects($this->once())
            ->method('end')
            ->with($this->callback(function ($s) use ($commits) {
                return $s->commits === $commits;
            }));
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->method('getCommitsSince')->with($startedAt)->willReturn($commits);
        $mockGit->method('getChangedFilesSince')->willReturn([]);
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Commits: 3', $output);
    }

    public function testExecuteCapturesChangedFiles(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Feature Work',
            startedAt: $startedAt
        );
        
        $files = [
            'src/Command/LoginCommand.php',
            'tests/Command/LoginCommandTest.php',
            'README.md'
        ];
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        $mockRepo->expects($this->once())
            ->method('end')
            ->with($this->callback(function ($s) use ($files) {
                return $s->filesChanges === $files;
            }));
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->method('getCommitsSince')->willReturn([]);
        $mockGit->method('getChangedFilesSince')->with($startedAt)->willReturn($files);
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Changed files:', $output);
        $this->assertStringContainsString('src/Command/LoginCommand.php', $output);
        $this->assertStringContainsString('tests/Command/LoginCommandTest.php', $output);
        $this->assertStringContainsString('README.md', $output);
    }

    public function testExecuteDisplaysDuration(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Long Session',
            startedAt: $startedAt
        );
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->method('getCommitsSince')->willReturn([]);
        $mockGit->method('getChangedFilesSince')->willReturn([]);
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Duration:', $output);
    }

    public function testExecuteSetsEndedAtTimestamp(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Test Session',
            startedAt: $startedAt
        );
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        $mockRepo->expects($this->once())
            ->method('end')
            ->with($this->callback(function ($s) use ($startedAt) {
                // Verify endedAt is set and is after startedAt
                return $s->endedAt !== null && 
                       $s->endedAt > $startedAt;
            }));
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->method('getCommitsSince')->willReturn([]);
        $mockGit->method('getChangedFilesSince')->willReturn([]);
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        ob_end_clean();
    }

    public function testExecuteHandlesNoChangedFiles(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Planning Session',
            startedAt: $startedAt
        );
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->method('getCommitsSince')->willReturn([]);
        $mockGit->method('getChangedFilesSince')->willReturn([]); // No files changed
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        // Should not show "Changed files:" section
        $this->assertStringNotContainsString('Changed files:', $output);
    }

    public function testExecuteCallsGitServiceWithCorrectTimestamp(): void
    {
        $startedAt = new DateTimeImmutable('2026-01-25 10:00:00');
        $session = new Session(
            id: 'test123',
            title: 'Test Session',
            startedAt: $startedAt
        );
        
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn($session);
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        $mockGit->expects($this->once())
            ->method('getCommitsSince')
            ->with($startedAt)
            ->willReturn([]);
        $mockGit->expects($this->once())
            ->method('getChangedFilesSince')
            ->with($startedAt)
            ->willReturn([]);
        
        $command = new EndCommand($mockRepo, $mockGit);
        
        ob_start();
        $command->execute();
        ob_end_clean();
    }
}
