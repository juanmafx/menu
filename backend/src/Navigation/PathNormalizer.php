<?php

declare(strict_types=1);

namespace Menu\Navigation;

final class PathNormalizer
{
    public static function normalize(string $requestUri): string
    {
        $path = substr($requestUri, 0, strcspn($requestUri, '?#'));
        $path = '/' . ltrim((string) preg_replace('#/+#', '/', $path), '/');
        if (str_ends_with($path, '/index.php')) {
            $path = substr($path, 0, -strlen('index.php'));
        }

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
