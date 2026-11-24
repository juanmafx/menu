<?php

declare(strict_types=1);

namespace Menu\Http;

final readonly class Response
{
    public function __construct(
        public int $status,
        public array $headers,
        public string $body,
    ) {
    }

    public function send(): void
    {
        http_response_code($this->status);
        header_remove('X-Powered-By');

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
