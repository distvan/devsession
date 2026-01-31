<?php

namespace DevSession\Util;

class Console
{
    public static function line(string $message): void
    {
        echo $message . PHP_EOL;
    }

    public static function success(string $message): void
    {
        echo "\033[32m✓\033[0m {$message}" . PHP_EOL;
    }

    public static function error(string $message): void
    {
        fwrite(STDERR, $message . PHP_EOL);
    }
}
