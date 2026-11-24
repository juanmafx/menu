<?php

declare(strict_types=1);

namespace Menu\Tests\Unit;

use Menu\Http\Request;
use Menu\Http\SecurityHeaders;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testBuildsFromServerValues(): void
    {
        $request = Request::fromGlobals(['REQUEST_METHOD' => 'get', 'REQUEST_URI' => '/about/?x=1', 'HTTPS' => 'on']);

        self::assertSame('GET', $request->method);
        self::assertSame('/about', $request->path);
        self::assertTrue($request->isSecure);
    }

    public function testDefaultsWhenServerValuesAreMissing(): void
    {
        $request = Request::fromGlobals([]);

        self::assertSame('GET', $request->method);
        self::assertSame('/', $request->path);
        self::assertFalse($request->isSecure);
    }

    public function testTreatsHttpsOffAsInsecure(): void
    {
        self::assertFalse(Request::fromGlobals(['HTTPS' => 'off'])->isSecure);
    }

    public function testSendsHstsOnlyOverHttps(): void
    {
        self::assertArrayNotHasKey('Strict-Transport-Security', SecurityHeaders::for(new Request('GET', '/')));
        self::assertArrayHasKey('Strict-Transport-Security', SecurityHeaders::for(new Request('GET', '/', true)));
    }
}
