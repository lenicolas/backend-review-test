<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\GitHubEventsImporterServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
#[AsCommand(name: 'app:import-github-events')]
class ImportGitHubEventsCommand extends Command
{
    private GitHubEventsImporterServiceInterface $eventsImporter;
    public function __construct(GitHubEventsImporterServiceInterface $eventsImporter)
    {
        parent::__construct();
        $this->eventsImporter = $eventsImporter;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events')
            ->setHelp('This command allows you to import GitHub Events from a date and hour')
            ->addArgument('date', InputArgument::REQUIRED, 'format Y-m-d')
            ->addArgument('hour', InputArgument::REQUIRED, 'format H');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $input->getArgument('date');
        $hour = $input->getArgument('hour');

        try {
            $this->eventsImporter->importEvents($date, $hour);
            $output->writeln('Command Import GH events executed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('Error executing command: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}
