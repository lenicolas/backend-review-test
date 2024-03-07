<?php

namespace App\Tests\Unit\Service;

use App\Service\GitHubEventsImporterService;
use App\Utils\FileStreamInterface;
use App\Utils\GzFileStreamInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GitHubEventsImporterServiceTest extends TestCase
{
    public function testImportEventsErrorOpeningFile(): void
    {
        $date = '2024-01-01';
        $hour = '15';
        $dateTime = $date . '-' . $hour;
        $filename = 'https://data.gharchive.org/' . $dateTime . '.json.gz';
        $jsonData = '{"key":"value"}';
        $em = $this->createMock(EntityManagerInterface::class);
        $file = $this->createMock(FileStreamInterface::class);
        $file->method('getFileContents')
             ->with($filename)
             ->willReturn($jsonData);

        $gzFile = $this->createMock(GzFileStreamInterface::class);
        $gzFile->method('gzOpen')
                ->with('data://text/plain;base64,' . base64_encode($jsonData))
                ->willReturn(false);


        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error opening the file: '. $filename);

        $importer = new GitHubEventsImporterService($em, $file, $gzFile);
        $importer->importEvents($date, $hour);
    }

}
