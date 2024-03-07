<?php

namespace App\Tests\Unit\Service;

use App\Service\GitHubEventsImporterService;
use App\Utils\FileStreamInterface;
use App\Utils\GzFileStreamInterface;
use Doctrine\ORM\EntityManagerInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class GitHubEventsImporterServiceTest extends TestCase
{
    private string $filename;
    private string $date;
    private string $hour;

    public function setUp(): void
    {
        $this->date = '2024-01-01';
        $this->hour = '15';
        $dateTime = $this->date . '-' . $this->hour;
        $this->filename = 'https://data.gharchive.org/' . $dateTime . '.json.gz';
    }

    public function testImportEventsErrorOpeningFile(): void
    {
        $jsonData = '{"key":"value"}';
        $em = $this->createMock(EntityManagerInterface::class);
        $file = $this->createMock(FileStreamInterface::class);
        $file->method('getFileContents')
             ->with($this->filename)
             ->willReturn($jsonData);

        $gzFile = $this->createMock(GzFileStreamInterface::class);
        $gzFile->method('gzOpen')
                ->with('data://text/plain;base64,' . base64_encode($jsonData))
                ->willReturn(false);


        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error opening the file');

        $importer = new GitHubEventsImporterService($em, $file, $gzFile);
        $importer->importEvents($this->date, $this->hour);
    }

    public function testImportEventsSuccess(): void
    {
        $eventJsonData = '{
                            "id": "34619792924",
                            "type": "PushEvent",
                            "actor": {
                                "id": 67855588,
                                "login": "test",
                                "display_login": "test",
                                "gravatar_id": "",
                                "url": "https:\/\/api.github.com\/users\/test",
                                "avatar_url": "https:\/\/avatars.githubusercontent.com\/u\/67855588?"
                            },
                            "repo": {
                                "id": 739791410,
                                "name": "test",
                                "url": "https:\/\/api.github.com\/repos\/test"
                            },
                            "payload": {
                                "key": "test"
                            },
                            "public": true,
                            "created_at": "2024-01-06T15:00:00Z"
                        }';

        $setup = vfsStream::setup();
        $fileSystem = new vfsStreamFile('test');
        $fileSystem->withContent($eventJsonData);
        $setup->addChild($fileSystem);
        $handle = fopen($fileSystem->url(), "r");
        // Mock EntityManager
        $em = $this->createMock(EntityManagerInterface::class);


        // Mock FileStream
        $file = $this->createMock(FileStreamInterface::class);
        $file->expects(self::once())
             ->method('getFileContents')
             ->with($this->filename)
             ->willReturn($fileSystem->getContent());

        // Mock Gz
        $gzFile = $this->createMock(GzFileStreamInterface::class);
        $gzFile->expects(self::once())
               ->method('gzOpen')
               ->with('data://text/plain;base64,' . base64_encode($eventJsonData))
               ->willReturn($handle);
        $gzFile->expects(self::any())
            ->method('gzEof')
            ->with($handle);

        $gzFile->expects(self::any())
            ->method('gzGets')
            ->with($handle)
            ->willReturn($eventJsonData);

        $gzFile->expects(self::once())
               ->method('gzClose')
               ->with($handle)
               ->willReturn(true);

        // Call method
        $importer = new GitHubEventsImporterService($em, $file, $gzFile);
        $importer->importEvents($this->date, $this->hour);
    }
}
