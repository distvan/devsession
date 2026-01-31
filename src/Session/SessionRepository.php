<?php

declare(strict_types=1);

namespace DevSession\Session;

use DateTimeImmutable;
use RuntimeException;

final class SessionRepository implements SessionRepositoryInterface
{
    private const FILE = __DIR__ . '/../../storage/sessions.json';

    public function __construct()
    {
        $this->ensureStorageDirectoryExists();
    }

    public function hasActiveSession(): bool
    {
        $sessions = $this->load();
        return isset($sessions['active']);
    }

    public function start(Session $session): void
    {
        $data = $this->load();

        $data['active'] = $session->id;
        $data['sessions'][] = $this->serialize($session);

        $this->save($data);
    }

    public function end(Session $session): void
    {
        $data = $this->load();

        foreach ($data['sessions'] as &$sessionData) {
            if ($sessionData['id'] === $session->id) {
                $sessionData['endedAt'] = $session->endedAt->format(DATE_ATOM);
                $sessionData['commits'] = $session->commits;
                $sessionData['filesChanges'] = $session->filesChanges;
                break;
            }
        }

        unset($data['active']);

        $this->save($data);
    }

    public function getCompletedSessions(?int $limit = null): array
    {
        $data = $this->load();
        $sessions = [];

        foreach ($data['sessions'] as $sessionData) {
            if (isset($sessionData['endedAt'])) {
                $sessions[] = $this->hydrate($sessionData);
            }
        }

        usort(
            $sessions,
            fn (Session $a, Session $b) => $b->endedAt <=> $a->endedAt
        );

        if ($limit !== null) {
            return array_slice($sessions, 0, $limit);
        }

        return $sessions;
    }


    public function getSessionsForDate(DateTimeImmutable $date): array
    {
        $data = $this->load();
        $sessions = [];

        foreach ($data['sessions'] as $sessionData) {
            $endedAt = isset($sessionData['endedAt']) ? new DateTimeImmutable($sessionData['endedAt']) : null;
            if($endedAt === null) {
                continue;
            }
            if ($endedAt->format('Y-m-d') === $date->format('Y-m-d')) {
                $sessions[] = $this->hydrate($sessionData);
            }
        }

        return $sessions;
    }

    public function getActiveSession(): ?Session
    {
        $data = $this->load();

        if (!isset($data['active'])) {
            return null;
        }

        foreach ($data['sessions'] as $sessionData) {
            if ($sessionData['id'] === $data['active']) {
                return $this->hydrate($sessionData);
            }
        }

        return null;
    }


    private function hydrate(array $data): Session
    {
        return new Session(
            id: $data['id'],
            title: $data['title'],
            startedAt: new DateTimeImmutable($data['startedAt']),
            gitRepo: $data['gitRepo'] ?? null,
            gitBranch: $data['gitBranch'] ?? null,
            endedAt: isset($data['endedAt']) ? new DateTimeImmutable($data['endedAt']) : null,
            commits: $data['commits'] ?? [],
            filesChanges: $data['filesChanges'] ?? []
        );
    }

    private function ensureStorageDirectoryExists(): void
    {
        $storageDir = dirname(self::FILE);

        if (!file_exists($storageDir)) {
            if (!mkdir($storageDir, 0755, true)) {
                throw new RuntimeException(
                    sprintf('Failed to create storage directory: %s', $storageDir)
                );
            }
        }

        if (!is_dir($storageDir)) {
            throw new RuntimeException(
                sprintf('Storage path exists but is not a directory: %s', $storageDir)
            );
        }

        if (!is_writable($storageDir)) {
            throw new RuntimeException(
                sprintf('Storage directory is not writable: %s', $storageDir)
            );
        }
    }

    private function load(): array
    {
        if (!file_exists(self::FILE)) {
            return ['sessions' => []];
        }

        $content = file_get_contents(self::FILE);
        
        if ($content === false) {
            throw new RuntimeException(
                sprintf('Failed to read sessions file: %s', self::FILE)
            );
        }

        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                sprintf('Invalid JSON in sessions file: %s', json_last_error_msg())
            );
        }

        return $data ?? ['sessions' => []];
    }

    private function save(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        if ($json === false) {
            throw new RuntimeException(
                sprintf('Failed to encode session data: %s', json_last_error_msg())
            );
        }

        if (file_put_contents(self::FILE, $json) === false) {
            throw new RuntimeException(
                sprintf('Failed to write sessions file: %s', self::FILE)
            );
        }
    }

    private function serialize(Session $session): array
    {
        return [
            'id' => $session->id,
            'title' => $session->title,
            'startedAt' => $session->startedAt->format(DATE_ATOM),
            'endedAt' => null,
            'gitRepo' => $session->gitRepo,
            'gitBranch' => $session->gitBranch,
            'commits' => [],
            'filesChanges' => [],
        ];
    }
}
