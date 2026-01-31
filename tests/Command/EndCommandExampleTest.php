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

/**
 * Example test showing how dependency injection enables mocking
 * This demonstrates the benefit of DI - we can now test EndCommand
 * without actually touching the filesystem or running git commands
 */
final class EndCommandExampleTest extends TestCase
{
    public function testCanNowMockDependencies(): void
    {
        // Create mock repository using the interface
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockGit = $this->createMock(GitServiceInterface::class);
        
        // Setup a fake active session
        $session = new Session(
            id: 'test123',
            title: 'Test Session',
            startedAt: new DateTimeImmutable('2026-01-25 10:00:00')
        );
        
        // Mock the repository to return our session
        $mockRepo->method('getActiveSession')->willReturn($session);
        
        // Mock git to return fake data
        $mockGit->method('getCommitsSince')->willReturn(['abc123 - Test commit']);
        $mockGit->method('getChangedFilesSince')->willReturn(['file1.php', 'file2.php']);
        
        // Expect the repository to save the session
        $mockRepo->expects($this->once())->method('end')->with($session);
        
        // Create command with mocked dependencies
        $command = new EndCommand($mockRepo, $mockGit);
        
        // Execute and capture output
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        // Assert the output contains expected information
        $this->assertStringContainsString('Test Session', $output);
        $this->assertStringContainsString('Session ended', $output);
    }
    
    public function testThrowsExceptionWhenNoActiveSession(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No active session to end.');
        
        // Mock repository with no active session using the interface
        $mockRepo = $this->createMock(SessionRepositoryInterface::class);
        $mockRepo->method('getActiveSession')->willReturn(null);
        
        $mockGit = $this->createMock(GitServiceInterface::class);
        
        $command = new EndCommand($mockRepo, $mockGit);
        $command->execute();
    }
}
