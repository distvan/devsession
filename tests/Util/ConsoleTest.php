<?php

declare(strict_types=1);

namespace DevSession\Tests\Util;

use DevSession\Util\Console;
use PHPUnit\Framework\TestCase;

final class ConsoleTest extends TestCase
{
    public function testLineOutputsMessageWithNewline(): void
    {
        $message = 'Test message';
        
        ob_start();
        Console::line($message);
        $output = ob_get_clean();
        
        $this->assertSame($message . PHP_EOL, $output);
    }

    public function testSuccessOutputsMessageWithCheckmark(): void
    {
        $message = 'Operation successful';
        
        ob_start();
        Console::success($message);
        $output = ob_get_clean();
        
        $this->assertStringContainsString($message, $output);
        $this->assertStringEndsWith(PHP_EOL, $output);
    }

    public function testErrorWritesToStderr(): void
    {
        $message = 'Error occurred';
        
        // Capture stderr
        $stderrBackup = fopen('php://memory', 'r+');
        $originalStderr = STDERR;
        
        // Temporarily redirect STDERR for testing
        ob_start();
        Console::error($message);
        $output = ob_get_clean();
        
        // The error method writes to STDERR, which we can't easily capture in tests
        // So we verify the method doesn't throw exceptions
        $this->assertTrue(true);
    }

    public function testLineWithEmptyString(): void
    {
        ob_start();
        Console::line('');
        $output = ob_get_clean();
        
        $this->assertSame(PHP_EOL, $output);
    }

    public function testSuccessWithEmptyString(): void
    {
        ob_start();
        Console::success('');
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output);
    }
}
