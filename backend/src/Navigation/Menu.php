<?php

declare(strict_types=1);

namespace Menu\Navigation;

use InvalidArgumentException;

final readonly class Menu
{
    private array $pagesByPath;

    public function __construct(array $pages)
    {
        $pagesByPath = [];
        foreach ($pages as $page) {
            if (isset($pagesByPath[$page->path])) {
                throw new InvalidArgumentException('Page paths must be unique.');
            }
            $pagesByPath[$page->path] = $page;
        }

        $this->pagesByPath = $pagesByPath;
    }

    public function pages(): array
    {
        return array_values($this->pagesByPath);
    }

    public function findByPath(string $path): ?Page
    {
        return $this->pagesByPath[$path] ?? null;
    }

    public function isActive(Page $page, string $currentPath): bool
    {
        return $page->path === $currentPath;
    }
}
