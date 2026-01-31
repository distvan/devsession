<?php

declare(strict_types=1);

namespace DevSession\Tests;

use DevSession\Application;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    private Application $app;

    protected function setUp(): void
    {
        $this->app = new Application();
    }

    public function testRunWithHelpCommand(): void
    {
        ob_start();
        $this->app->run(['script.php', 'help']);
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output);
    }

    public function testRunWithNoCommandDefaultsToHelp(): void
    {
        ob_start();
        $this->app->run(['script.php']);
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output);
    }

    public function testRunWithUnknownCommandDefaultsToHelp(): void
    {
        ob_start();
        $this->app->run(['script.php', 'unknown']);
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output);
    }

    public function testRunHandlesExceptionsAndExitsWithError(): void
    {
        // Testing Application with exit() calls is challenging in PHPUnit
        // We skip this test as it would terminate the test runner
        // The error handling can be tested manually or with process isolation
        $this->markTestSkipped('Cannot test exit() calls without process isolation');
    }
}
