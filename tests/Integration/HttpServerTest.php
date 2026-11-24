<?php

declare(strict_types=1);

namespace Menu\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HttpServerTest extends TestCase
{
    private static mixed $process = null;
    private static int $port = 0;

    public static function setUpBeforeClass(): void
    {
        $probe = stream_socket_server('tcp://127.0.0.1:0');
        if ($probe === false) {
            throw new RuntimeException('Unable to allocate a local port.');
        }
        self::$port = (int) substr((string) strrchr((string) stream_socket_get_name($probe, false), ':'), 1);
        fclose($probe);

        $root = dirname(__DIR__, 2);
        self::$process = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:' . self::$port, '-t', $root . '/frontend/public', $root . '/backend/dev/router.php'],
            [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipes,
        );

        for ($attempt = 0; $attempt < 100; $attempt++) {
            $connection = @fsockopen('127.0.0.1', self::$port, $errorCode, $errorMessage, 0.1);
            if ($connection !== false) {
                fclose($connection);

                return;
            }
            usleep(50_000);
        }

        throw new RuntimeException('The development server did not start.');
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$process)) {
            proc_terminate(self::$process);
            proc_close(self::$process);
        }
    }

    public static function routeProvider(): array
    {
        return [
            'home' => ['/', 200],
            'about' => ['/about', 200],
            'contact with slash' => ['/contact/', 200],
            'unknown' => ['/missing', 404],
            'source file is not served' => ['/backend/config/pages.php', 404],
            'encoded null byte' => ['/assets/%00', 404],
            'encoded traversal' => ['/assets/%2e%2e/%2e%2e/%2e%2e/composer.json', 404],
        ];
    }

    #[DataProvider('routeProvider')]
    public function testRoutes(string $path, int $expectedStatus): void
    {
        self::assertSame($expectedStatus, $this->request('GET', $path)['status']);
    }

    public function testServesStylesheet(): void
    {
        $response = $this->request('GET', '/assets/css/main.css');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('.menu', $response['body']);
    }

    public function testActiveEntryFollowsTheRequest(): void
    {
        $body = $this->request('GET', '/about')['body'];

        self::assertStringContainsString('<li class="active"><a href="/about" aria-current="page">About</a></li>', $body);
    }

    public function testHostHeaderIsNotReflected(): void
    {
        $response = $this->request('GET', '/', 'attacker.invalid');

        self::assertStringNotContainsString('attacker.invalid', $response['body']);
    }

    public function testSendsSecurityHeadersAndHidesRuntime(): void
    {
        $headers = strtolower($this->request('GET', '/')['headers']);

        self::assertStringContainsString('content-security-policy:', $headers);
        self::assertStringContainsString('x-content-type-options: nosniff', $headers);
        self::assertStringNotContainsString('x-powered-by', $headers);
    }

    public function testRejectsPost(): void
    {
        self::assertSame(405, $this->request('POST', '/')['status']);
    }

    private function request(string $method, string $path, string $host = '127.0.0.1'): array
    {
        $socket = fsockopen('127.0.0.1', self::$port, $errorCode, $errorMessage, 2.0);
        if ($socket === false) {
            throw new RuntimeException('Unable to connect to the development server.');
        }

        fwrite($socket, sprintf("%s %s HTTP/1.1\r\nHost: %s\r\nConnection: close\r\nContent-Length: 0\r\n\r\n", $method, $path, $host));
        $raw = (string) stream_get_contents($socket);
        fclose($socket);

        [$headers, $body] = array_pad(explode("\r\n\r\n", $raw, 2), 2, '');
        preg_match('#\AHTTP/\d\.\d (\d{3})#', $headers, $matches);

        return ['status' => (int) ($matches[1] ?? 0), 'headers' => $headers, 'body' => $body];
    }
}
