<?php

declare(strict_types=1);

namespace App\Utils;

class GzFileStream implements GzFileStreamInterface
{
    public function gzOpen($filename, $mode): mixed
    {
        return gzopen($filename, $mode);
    }

    public function gzClose($stream): bool
    {
        return gzclose($stream);
    }
}
