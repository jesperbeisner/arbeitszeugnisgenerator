<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Stdlib;

use RuntimeException;

final readonly class Request
{
    /**
     * @param array<string, string|int|float> $server
     * @param array<string, string> $get
     * @param array<string, string> $post
     */
    public function __construct(
        public array $server,
        public array $get,
        public array $post,
    ) {
    }

    public static function fromGlobals(): Request
    {
        return new Request($_SERVER, $_GET, $_POST);
    }

    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? throw new RuntimeException('Uri not found?!');

        if (!is_string($uri)) {
            throw new RuntimeException('Uri not a string?!');
        }

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        return rawurldecode($uri);
    }

    public function getMethod(): string
    {
        $method = $this->server['REQUEST_METHOD'] ?? throw new RuntimeException('Method not found?!');

        if (!is_string($method)) {
            throw new RuntimeException('Method not a string?!');
        }

        return strtoupper($method);
    }
}
