<?php

namespace App\Utils;

interface FileStreamInterface
{
    public function getFileContents(string $filename): bool|string;
}
