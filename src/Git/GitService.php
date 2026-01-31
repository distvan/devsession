<?php

declare(strict_types=1);

namespace DevSession\Git;

/**
 * Service to interact with Git repositories.
 */
final class GitService implements GitServiceInterface
{
    /**
     * Checks if git is available on the system.
     */
    private function isGitAvailable(): bool
    {
        exec('git --version 2>&1', $output, $code);
        return $code === 0;
    }

    /**
     * Checks if the current directory is a Git repository.
     */
    private function isGitRepository(): bool
    {
        if (!$this->isGitAvailable()) {
            return false;
        }

        exec('git rev-parse --is-inside-work-tree 2>&1', $output, $code);
        return $code === 0;
    }

    /**
     * Gets the repository name.
     */
    public function getRepositoryName(): ?string
    {
        if (!$this->isGitRepository()) {
            return null;
        }

        exec('git rev-parse --show-toplevel 2>&1', $output, $code);
        
        if ($code !== 0 || empty($output)) {
            return null;
        }

        $topLevel = trim($output[0]);
        return $topLevel ? basename($topLevel) : null;
    }

    /**
     * Gets the current branch name of the Git repository.
     */
    public function getCurrentBranch(): ?string
    {
        if (!$this->isGitRepository()) {
            return null;
        }

        exec('git branch --show-current 2>&1', $output, $code);

        if ($code !== 0 || empty($output)) {
            return null;
        }

        $branch = trim($output[0]);
        return $branch !== '' ? $branch : null;
    }

    /**
     * Gets commits since a specific date.
     */
    public function getCommitsSince(\DateTimeImmutable $since): array
    {
        if (!$this->isGitRepository()) {
            return [];
        }

        $sinceIso = $since->format(DATE_ATOM);
        $command = sprintf('git log --since="%s" --pretty=format:"%%h - %%s" 2>&1', $sinceIso);
        
        exec($command, $output, $code);

        if ($code !== 0) {
            return [];
        }

        return array_filter($output);
    }

    /**
     * Gets files changed since a specific date.
     */
    public function getChangedFilesSince(\DateTimeImmutable $since): array
    {
        if (!$this->isGitRepository()) {
            return [];
        }

        $sinceIso = $since->format(DATE_ATOM);
        $command = sprintf('git log --since="%s" --name-only --pretty=format: 2>&1', $sinceIso);
        
        exec($command, $output, $code);

        if ($code !== 0) {
            return [];
        }

        return array_values(array_unique(array_filter($output)));
    }
}
