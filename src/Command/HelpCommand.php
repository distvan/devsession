<?php

declare(strict_types=1);

namespace DevSession\Command;

use DevSession\Util\Console;

final class HelpCommand implements CommandInterface
{
    public function execute(): void
    {
        Console::line('DevSession - Development Session Assistant');
        Console::line('');
        Console::line('Usage: devsession <command> [options]');
        Console::line('');
        Console::line('Commands:');
        Console::line('  start  "Session title"     Start a new development session');
        Console::line('  end                        End the active session');
        Console::line('  status                     Show the active session');
        Console::line('  today                      Show todays\'s session');
        Console::line('  log                        Show session history');
        Console::line('  help                       Show this help');
        Console::line('');
    }
}