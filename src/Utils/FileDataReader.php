<?php

namespace App\Utils;

use App\Utils\FileDataReaderInterface;

class FileDataReader implements FileDataReaderInterface
{
    private FileStreamInterface $fileStream;
    private GzFileStreamInterface $gzFileStream;

    public function __construct(FileStreamInterface $fileStream, GzFileStreamInterface $gzFileStream)
    {
        $this->fileStream = $fileStream;
        $this->gzFileStream = $gzFileStream;
    }

    public function getFileData(string $filename): string
    {
        $jsonData = $this->fileStream->getFileContents($filename);

        if (false === $jsonData) {
            throw new \RuntimeException('Error getting file contents: '.$filename);
        }

        return (string) $jsonData;
    }

    /**
     * @return resource
     */
    public function openFile(string $jsonData)
    {
        $handle = $this->gzFileStream->gzOpen('data://text/plain;base64,'.base64_encode($jsonData), 'rb');

        if (!$handle) {
            throw new \RuntimeException('Error opening the file');
        }

        return $handle;
    }

    /**
     * @param resource $handle
     */
    public function isEndOfFile($handle): bool
    {
        return true === $this->gzFileStream->gzEof($handle);
    }

    /**
     * @param resource $handle
     */
    public function readLine($handle): string
    {
        $line = $this->gzFileStream->gzGets($handle, 4096);

        if (false === $line) {
            throw new \RuntimeException('Error reading line');
        }

        return $line;
    }
}
