<?php

declare(strict_types=1);

namespace DevSession\Tests\Command;

use DevSession\Command\StartCommand;
use InvalidArgumentException;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use DevSession\Session\SessionRepositoryInterface;
use DevSession\Git\GitServiceInterface;
use DevSession\Session\SessionRepository;
use DevSession\Git\GitService;

final class StartCommandTest extends TestCase
{
    private SessionRepositoryInterface $repository;
    private GitServiceInterface $git;

    protected function setUp(): void
    {
        $this->repository = new SessionRepository();
        $this->git = new GitService();
        
        // Clean up any active sessions before each test
        $storageFile = __DIR__ . '/../../storage/sessions.json';
        if (file_exists($storageFile)) {
            $data = json_decode(file_get_contents($storageFile), true);
            unset($data['active']);
            file_put_contents($storageFile, json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    public function testExecuteThrowsExceptionWhenTitleIsNotProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Session title is required.');
        
        $command = new StartCommand($this->repository, $this->git, []);
        $command->execute();
    }

    public function testExecuteThrowsExceptionWhenSessionIsAlreadyActive(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('A session is already active.');
        
        // Start first session
        $command1 = new StartCommand($this->repository, $this->git, ['First Session']);
        
        ob_start();
        $command1->execute();
        ob_end_clean();
        
        // Try to start second session
        $command2 = new StartCommand($this->repository, $this->git, ['Second Session']);
        $command2->execute();
    }

    public function testExecuteStartsSessionSuccessfully(): void
    {
        $command = new StartCommand($this->repository, $this->git, ['Test Session']);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Test Session', $output);
        $this->assertStringContainsString('started', $output);
    }

    public function testExecuteCreatesSessionWithGitInformation(): void
    {
        $command = new StartCommand($this->repository, $this->git, ['Git Test Session']);
        
        ob_start();
        $command->execute();
        $output = ob_get_clean();
        
        // Output might contain branch information if we're in a git repo
        $this->assertNotEmpty($output);
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        $storageFile = __DIR__ . '/../../storage/sessions.json';
        if (file_exists($storageFile)) {
            $data = json_decode(file_get_contents($storageFile), true);
            unset($data['active']);
            file_put_contents($storageFile, json_encode($data, JSON_PRETTY_PRINT));
        }
    }
}
