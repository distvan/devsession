<?php

declare(strict_types=1);

namespace DevSession;

use DevSession\Git\GitService;
use DevSession\Git\GitServiceInterface;
use DevSession\Session\SessionRepository;
use DevSession\Session\SessionRepositoryInterface;

final class ServiceContainer
{
    private ?SessionRepositoryInterface $sessionRepository = null;
    private ?GitServiceInterface $gitService = null;

    public function getSessionRepository(): SessionRepositoryInterface
    {
        if ($this->sessionRepository === null) {
            $this->sessionRepository = new SessionRepository();
        }

        return $this->sessionRepository;
    }

    public function getGitService(): GitServiceInterface
    {
        if ($this->gitService === null) {
            $this->gitService = new GitService();
        }

        return $this->gitService;
    }

    public function setSessionRepository(SessionRepositoryInterface $repository): void
    {
        $this->sessionRepository = $repository;
    }

    public function setGitService(GitServiceInterface $gitService): void
    {
        $this->gitService = $gitService;
    }
}
