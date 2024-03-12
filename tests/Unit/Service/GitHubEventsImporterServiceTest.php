<?php

namespace App\Tests\Unit\Service;

use App\Service\GitHubEventsImporterService;
use App\Repository\ReadEventRepositoryInterface;
use App\Utils\FileDataReaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class GitHubEventsImporterServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private FileDataReaderInterface $fileDataReader;
    private ReadEventRepositoryInterface $readEventRepository;
    private GitHubEventsImporterService $gitHubEventsImporterService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fileDataReader = $this->createMock(FileDataReaderInterface::class);
        $this->readEventRepository = $this->createMock(ReadEventRepositoryInterface::class);

        $this->gitHubEventsImporterService = new GitHubEventsImporterService(
            $this->entityManager,
            $this->fileDataReader,
            $this->readEventRepository
        );
    }

    public function testImportEvents()
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

        $this->fileDataReader
            ->method('getFileData')
            ->willReturn('data');

        $setup = vfsStream::setup();
        $fileSystem = new vfsStreamFile('test');
        $fileSystem->withContent($eventJsonData);
        $setup->addChild($fileSystem);
        $handle = fopen($fileSystem->url(), "r");

        $this->fileDataReader
            ->method('openFile')
            ->willReturn($handle);
        $this->fileDataReader
            ->method('isEndOfFile')
            ->with($handle)
            ->willReturn(false, true);
        $this->fileDataReader
            ->method('readLine')
            ->with($handle)
            ->willReturn($eventJsonData);

        $this->readEventRepository
            ->method('exist')
            ->with('34619792924')
            ->willReturn(false);

        $this->entityManager
            ->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->gitHubEventsImporterService->importEvents('2022-01-01', '00');
    }

    public function testImportEventsWithInvalidEvent()
    {
        $eventJsonData = '{
                            "test"
                        }';

        $this->fileDataReader
            ->method('getFileData')
            ->willReturn('data');

        $setup = vfsStream::setup();
        $fileSystem = new vfsStreamFile('test');
        $fileSystem->withContent($eventJsonData);
        $setup->addChild($fileSystem);
        $handle = fopen($fileSystem->url(), "r");

        $this->fileDataReader
            ->method('openFile')
            ->willReturn($handle);
        $this->fileDataReader
            ->method('isEndOfFile')
            ->with($handle)
            ->willReturn(false, true);
        $this->fileDataReader
            ->method('readLine')
            ->with($handle)
            ->willReturn($eventJsonData);

        $this->readEventRepository
            ->expects($this->never())
            ->method('exist')
            ->with('test')
            ->willReturn(false);

        $this->entityManager
            ->expects($this->never())
            ->method('persist');
        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->gitHubEventsImporterService->importEvents('2022-01-01', '00');
    }

    public function testImportEventsWithExistEvent()
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
        $this->fileDataReader
            ->method('getFileData')
            ->willReturn('data');

        $setup = vfsStream::setup();
        $fileSystem = new vfsStreamFile('test');
        $fileSystem->withContent($eventJsonData);
        $setup->addChild($fileSystem);
        $handle = fopen($fileSystem->url(), "r");

        $this->fileDataReader
            ->method('openFile')
            ->willReturn($handle);
        $this->fileDataReader
            ->method('isEndOfFile')
            ->with($handle)
            ->willReturn(false, true);
        $this->fileDataReader
            ->method('readLine')
            ->with($handle)
            ->willReturn($eventJsonData);

        $this->readEventRepository
            ->method('exist')
            ->with('34619792924')
            ->willReturn(true);

        $this->entityManager
            ->expects($this->never())
            ->method('persist');
        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->gitHubEventsImporterService->importEvents('2022-01-01', '00');
    }
}
