<?php

namespace App\Tests\Unit\Command;

use App\Command\ImportGitHubEventsCommand;
use App\Service\GitHubEventsImporterServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Tester\CommandTester;

class ImportGitHubEventsCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $application = new Application();
        $gitHubEventsImporterService = $this->createMock(GitHubEventsImporterServiceInterface::class);

        $application->add(new ImportGitHubEventsCommand($gitHubEventsImporterService));

        $command = $application->find('app:import-github-events');
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute();
        $this->assertSame(Command::SUCCESS, $result);

    }
}
