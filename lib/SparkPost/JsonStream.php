<?php

namespace SparkPost;

use Psr\Http\Message\StreamInterface;

class JsonStream implements StreamInterface
{
    private $stream;

    public function __construct(array $data)
    {
        $this->stream = fopen('php://memory', 'r+');
        fwrite($this->stream, json_encode($data));
        rewind($this->stream);
    }

    public function __toString(): string
    {
        return stream_get_contents($this->stream, -1, 0);
    }

    public function close(): void
    {
        fclose($this->stream);
    }

    public function detach()
    {
        $result = $this->stream;
        $this->close();
        return $result;
    }

    public function getSize(): ?int
    {
        $stats = fstat($this->stream);
        return $stats['size'] ?? null;
    }

    public function tell(): int
    {
        return ftell($this->stream);
    }

    public function eof(): bool
    {
        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        $meta = stream_get_meta_data($this->stream);
        return $meta['seekable'];
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        fseek($this->stream, $offset, $whence);
    }

    public function rewind(): void
    {
        rewind($this->stream);
    }

    public function isWritable(): bool
    {
        $meta = stream_get_meta_data($this->stream);
        return is_writable($meta['uri']);
    }

    public function write(string $string): int
    {
        return fwrite($this->stream, $string);
    }

    public function isReadable(): bool
    {
        $meta = stream_get_meta_data($this->stream);
        return is_readable($meta['uri']);
    }

    public function read(int $length): string
    {
        return fread($this->stream, $length);
    }

    public function getContents(): string
    {
        return stream_get_contents($this->stream);
    }

    public function getMetadata(?string $key = null)
    {
        $meta = stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
