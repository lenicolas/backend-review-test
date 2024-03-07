<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\FileStreamInterface;
use App\Utils\GzFileStreamInterface;
use Doctrine\ORM\EntityManagerInterface;

class GitHubEventsImporterService implements GitHubEventsImporterServiceInterface
{
    private EntityManagerInterface $entityManager;
    private FileStreamInterface $fileStream;
    private GzFileStreamInterface $gzFileStream;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileStreamInterface $fileStream,
        GzFileStreamInterface $gzFileStream
    )
    {
        $this->entityManager = $entityManager;
        $this->fileStream = $fileStream;
        $this->gzFileStream = $gzFileStream;
    }

    public function importEvents(string $date, string $hour): void
    {
        $dateTime = $date . '-' . $hour;
        $filename = 'https://data.gharchive.org/' . $dateTime . '.json.gz';
        $jsonData = $this->fileStream->getFileContents($filename);

        $handle = $this->gzFileStream->gzOpen('data://text/plain;base64,' . base64_encode($jsonData), 'rb');

        if (!$handle) {
            throw new \RuntimeException('Error opening the file: ' . $filename);
        }
    }
}
