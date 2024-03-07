<?php

namespace App\Utils;

interface GzFileStreamInterface
{
    public function gzOpen(string $filename, string $mode): mixed;

    /**
     * @param resource $stream
     * @return bool
     */
    public function gzClose($stream): bool;

    /**
     * @param resource $stream
     * @return bool
     */
    public function gzEof($stream): bool;

    /**
     * @param resource $stream
     * @param int|null $length
     * @return string|false
     */
    public function gzGets($stream, ?int $length): string|false;
}
