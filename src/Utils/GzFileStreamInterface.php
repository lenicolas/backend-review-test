<?php

namespace App\Utils;

interface GzFileStreamInterface
{
    public function gzOpen(string $filename, string $mode): mixed;

    /**
     * @param resource $stream
     */
    public function gzClose($stream): bool;

    /**
     * @param resource $stream
     */
    public function gzEof($stream): bool;

    /**
     * @param resource $stream
     */
    public function gzGets($stream, ?int $length): string|false;
}
