<?php

declare(strict_types=1);

namespace DevSession;

use DevSession\Util\Console;
use DevSession\Command\StartCommand;
use DevSession\Command\EndCommand;
use DevSession\Command\StatusCommand;
use DevSession\Command\TodayCommand;
use DevSession\Command\LogCommand;
use DevSession\Command\HelpCommand;

final class Application
{
    public function __construct(
        private ?ServiceContainer $container = null
    ) {
        $this->container ??= new ServiceContainer();
    }

    public function run(array $argv): void
    {
        array_shift($argv);
        $commandName = $argv[0] ?? 'help';
        $arguments = array_slice($argv, 1);

        $command = match ($commandName) {
            'start' => new StartCommand(
                $this->container->getSessionRepository(),
                $this->container->getGitService(),
                $arguments
            ),
            'end' => new EndCommand(
                $this->container->getSessionRepository(),
                $this->container->getGitService()
            ),
            'status' => new StatusCommand(
                $this->container->getSessionRepository()
            ),
            'today' => new TodayCommand(
                $this->container->getSessionRepository()
            ),
            'log' => new LogCommand(
                $this->container->getSessionRepository(),
                $arguments
            ),
            'help' => new HelpCommand(),
            default => new HelpCommand(),
        };

        try {
            $command->execute();
        } catch (\Throwable $e) {
            Console::error($e->getMessage());
            exit(1);
        }
    }
}
