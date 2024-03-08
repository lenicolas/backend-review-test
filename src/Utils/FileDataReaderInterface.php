<?php

namespace App\Utils;

interface FileDataReaderInterface
{
    public function getFileData(string $filename): string;
    public function openFile(string $jsonData);
    public function isEndOfFile($handle): bool;
    public function readLine($handle): string;
}
