<?php

namespace App\Utils;

interface GzFileStreamInterface
{
    public function gzOpen(string $filename, string $mode): mixed;
    public function gzClose($stream): bool;
}
