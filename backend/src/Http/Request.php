<?php

declare(strict_types=1);

namespace Menu\Http;

use Menu\Navigation\PathNormalizer;

final readonly class Request
{
    public function __construct(
        public string $method,
        public string $path,
        public bool $isSecure = false,
    ) {
    }

    public static function fromGlobals(array $server): self
    {
        $method = $server['REQUEST_METHOD'] ?? 'GET';
        $uri = $server['REQUEST_URI'] ?? '/';
        $https = $server['HTTPS'] ?? '';

        return new self(
            is_string($method) ? strtoupper($method) : 'GET',
            PathNormalizer::normalize(is_string($uri) ? $uri : '/'),
            is_string($https) && $https !== '' && strtolower($https) !== 'off',
        );
    }

    public function isHead(): bool
    {
        return $this->method === 'HEAD';
    }
}
