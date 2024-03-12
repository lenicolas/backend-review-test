<?php

namespace App\Tests\Unit\Utils;

use App\Utils\FileDataReader;
use App\Utils\FileStreamInterface;
use App\Utils\GzFileStreamInterface;
use PHPUnit\Framework\TestCase;

class FileDataReaderTest extends TestCase
{
    private FileStreamInterface $fileStream;
    private GzFileStreamInterface $gzFileStream;
    private FileDataReader $fileDataReader;

    protected function setUp(): void
    {
        $this->fileStream = $this->createMock(FileStreamInterface::class);
        $this->gzFileStream = $this->createMock(GzFileStreamInterface::class);

        $this->fileDataReader = new FileDataReader(
            $this->fileStream,
            $this->gzFileStream
        );
    }

    public function testGetFileData()
    {
        $filename = 'test.json';
        $jsonData = '{"test": "data"}';

        $this->fileStream->expects($this->once())
            ->method('getFileContents')
            ->with($filename)
            ->willReturn($jsonData);

        $result = $this->fileDataReader->getFileData($filename);

        $this->assertEquals($jsonData, $result);
    }

    public function testOpenFile()
    {
        $jsonData = '{"test": "data"}';
        $handle = tmpfile();

        $this->gzFileStream->expects($this->once())
            ->method('gzOpen')
            ->with('data://text/plain;base64,'.base64_encode($jsonData), 'rb')
            ->willReturn($handle);

        $result = $this->fileDataReader->openFile($jsonData);

        $this->assertEquals($handle, $result);
    }

    public function testIsEndOfFile()
    {
        $handle = tmpfile();

        $this->gzFileStream->expects($this->once())
            ->method('gzEof')
            ->with($handle)
            ->willReturn(true);

        $result = $this->fileDataReader->isEndOfFile($handle);

        $this->assertTrue($result);
    }

    public function testReadLine()
    {
        $handle = tmpfile();
        $line = 'test line';

        $this->gzFileStream->expects($this->once())
            ->method('gzGets')
            ->with($handle, 4096)
            ->willReturn($line);

        $result = $this->fileDataReader->readLine($handle);

        $this->assertEquals($line, $result);
    }
}
