<?php

namespace App\Utils;

interface FileDataReaderInterface
{
    public function getFileData(string $filename): string;

    /** @return resource */
    public function openFile(string $jsonData);

    /** @param resource $handle */
    public function isEndOfFile($handle): bool;
    /** @param resource $handle */
    public function readLine($handle): string;
}
