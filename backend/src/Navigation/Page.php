<?php

declare(strict_types=1);

namespace Menu\Navigation;

use InvalidArgumentException;

final readonly class Page
{
    public function __construct(
        public string $path,
        public string $label,
        public string $title,
        public string $description,
        public string $template,
    ) {
        if (PathNormalizer::normalize($path) !== $path) {
            throw new InvalidArgumentException('Page path must be normalized.');
        }

        if (preg_match('/\A[a-z0-9-]+\z/', $template) !== 1) {
            throw new InvalidArgumentException('Page template name is invalid.');
        }
    }
}
