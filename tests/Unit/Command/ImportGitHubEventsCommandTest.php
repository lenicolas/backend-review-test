<?php

namespace App\Tests\Func;

use App\Command\ImportGitHubEventsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportGitHubEventsCommandeTest extends TestCase
{
    public function testExecute(): void
    {
        $application = new Application();
        $application->add(new ImportGitHubEventsCommand());

        $command = $application->find('app:import-github-events');
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute([]);
        $this->assertSame(1, $result);

    }
}
