<?php

declare(strict_types=1);

namespace Menu\Tests\Unit;

use Menu\Navigation\PathNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PathNormalizerTest extends TestCase
{
    public static function uriProvider(): array
    {
        return [
            'root' => ['/', '/'],
            'empty' => ['', '/'],
            'query only' => ['?page=1', '/'],
            'front controller' => ['/index.php', '/'],
            'plain path' => ['/about', '/about'],
            'trailing slash' => ['/about/', '/about'],
            'query string' => ['/about?ref=x', '/about'],
            'fragment' => ['/about#team', '/about'],
            'duplicate slashes' => ['//about///', '/about'],
            'nested front controller' => ['/about/index.php', '/about'],
            'encoded traversal stays literal' => ['/%2e%2e/secret', '/%2e%2e/secret'],
            'absolute form' => ['http://example.test/about?ref=x', '/about'],
            'absolute form without path' => ['https://example.test', '/'],
            'scheme inside path stays literal' => ['/http://example.test/about', '/http:/example.test/about'],
        ];
    }

    #[DataProvider('uriProvider')]
    public function testNormalizesRequestUris(string $uri, string $expected): void
    {
        self::assertSame($expected, PathNormalizer::normalize($uri));
    }
}
