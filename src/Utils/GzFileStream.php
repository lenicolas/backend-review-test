<?php

declare(strict_types=1);

namespace App\Utils;

class GzFileStream implements GzFileStreamInterface
{
    public function gzOpen(string $filename, string $mode): mixed
    {
        return gzopen($filename, $mode);
    }

    /**
     * @param resource $stream
     * @return bool
     */
    public function gzClose($stream): bool
    {
        return gzclose($stream);
    }

    /**
     * @param resource $stream
     * @return bool
     */
    public function gzEof($stream): bool
    {
        return gzeof($stream);
    }

    /**
     * @param resource $stream
     * @param int|null $length
     * @return string|false
     */
    public function gzGets($stream, ?int $length = null): string|false
    {
        return gzgets($stream, $length);
    }
}
