<?php

namespace App\Tests\Unit\Command;

use App\Command\ImportGitHubEventsCommand;
use App\Service\GitHubEventsImporterServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ImportGitHubEventsCommandTest extends TestCase
{
    public function testExecuteSucess(): void
    {
        $date = '2024-01-01';
        $hour = '15';

        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')
             ->willReturnMap([
                                 ['date', $date],
                                 ['hour', $hour]
                             ]);

        $importer = $this->createMock(GitHubEventsImporterServiceInterface::class);
        $importer->expects($this->once())
                 ->method('importEvents')
                 ->with($date, $hour);

        // Create ImportGitHubEventsCommand instance and inject the mock importer
        $command = new ImportGitHubEventsCommand($importer);
        $tester = new CommandTester($command);
        $result = $tester->execute(['date' => $date, 'hour' => $hour]);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertStringContainsString('Command Import GH events executed successfully.', $tester->getDisplay());
    }

    public function testExecuteFailure(): void
    {
        $date = '2024-01-01';
        $hour = '15';

        $this->createMock(InputInterface::class)
            ->method('getArgument')
            ->willReturnMap([
                ['date', $date],
                ['hour', $hour]
            ]);

        $importer = $this->createMock(GitHubEventsImporterServiceInterface::class);
        $importer->expects($this->once())
                 ->method('importEvents')
                 ->willThrowException(new \Exception('test failure'))
                 ->with($date, $hour);

        // Create ImportGitHubEventsCommand instance and inject the mock importer
        $command = new ImportGitHubEventsCommand($importer);
        $tester = new CommandTester($command);
        $result = $tester->execute(['date' => $date, 'hour' => $hour]);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString('Error executing command import-github-events: test failure', $tester->getDisplay());
    }
}
