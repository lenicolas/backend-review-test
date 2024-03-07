<?php

declare(strict_types=1);

namespace App\Utils;

class FileStream implements FileStreamInterface
{
    public function getFileContents(string $filename): bool|string
    {
        return file_get_contents($filename);
    }
}
