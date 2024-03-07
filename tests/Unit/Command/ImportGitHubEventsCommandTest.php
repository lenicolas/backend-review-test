<?php

namespace App\Tests\Unit\Command;

use App\Command\ImportGitHubEventsCommand;
use App\Service\GitHubEventsImporterServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ImportGitHubEventsCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $date = '2024-01-01';
        $hour = '15';

        $this->createMock(InputInterface::class)
             ->method('getArgument')
             ->willReturnMap([
                                 ['date', $date],
                                 ['hour', $hour]
                             ]);

        $importer = $this->createMock(GitHubEventsImporterServiceInterface::class)
                         ->expects($this->once())
                         ->method('importEvents')
                         ->with($date, $hour);

        // Create ImportGitHubEventsCommand instance and inject the mock importer
        $command = new ImportGitHubEventsCommand($importer);
        $tester = new CommandTester($command);
        $result = $tester->execute(['date' => $date, 'hour' => $hour]);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertStringContainsString('Command Import GH events executed successfully.', $tester->getDisplay());

    }
}
