<?php

declare(strict_types=1);

namespace Menu\Tests\Unit;

use Menu\Application;
use Menu\Http\Request;
use Menu\Navigation\Menu;
use Menu\Navigation\Page;
use Menu\View\TemplateRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    public static function pathProvider(): array
    {
        return [
            'home' => ['/', 'Home'],
            'about' => ['/about', 'About'],
            'contact' => ['/contact', 'Contact'],
        ];
    }

    #[DataProvider('pathProvider')]
    public function testHighlightsExactlyTheCurrentPage(string $path, string $label): void
    {
        $response = $this->application()->handle(new Request('GET', $path));

        self::assertSame(200, $response->status);
        self::assertSame(1, substr_count($response->body, 'class="active"'));
        self::assertSame(1, substr_count($response->body, 'aria-current="page"'));
        self::assertStringContainsString('aria-current="page">' . $label . '</a>', $response->body);
    }

    public function testUnknownPathReturnsNotFoundWithoutReflectingInput(): void
    {
        $response = $this->application()->handle(new Request('GET', '/<script>alert(1)</script>'));

        self::assertSame(404, $response->status);
        self::assertStringNotContainsString('<script', $response->body);
        self::assertStringNotContainsString('class="active"', $response->body);
    }

    public function testRejectsUnsupportedMethod(): void
    {
        $response = $this->application()->handle(new Request('POST', '/'));

        self::assertSame(405, $response->status);
        self::assertSame('GET, HEAD', $response->headers['Allow']);
    }

    public function testHeadRequestHasNoBody(): void
    {
        $response = $this->application()->handle(new Request('HEAD', '/about'));

        self::assertSame(200, $response->status);
        self::assertSame('', $response->body);
    }

    public function testEscapesConfiguredLabels(): void
    {
        $page = new Page('/', '<b>Home</b>', '<i>Title</i>', '"quoted"', 'home');
        $application = new Application(new Menu([$page]), $this->renderer());

        $body = $application->handle(new Request('GET', '/'))->body;

        self::assertStringNotContainsString('<b>Home</b>', $body);
        self::assertStringNotContainsString('<i>Title</i>', $body);
        self::assertStringContainsString('&lt;b&gt;Home&lt;/b&gt;', $body);
    }

    public function testRenderFailureReturnsGenericError(): void
    {
        $page = new Page('/', 'Home', 'Title', 'Description', 'does-not-exist');
        $application = new Application(new Menu([$page]), $this->renderer());
        $previousLog = ini_set('error_log', '/dev/null');

        $response = $application->handle(new Request('GET', '/'));
        ini_set('error_log', (string) $previousLog);

        self::assertSame(500, $response->status);
        self::assertSame('Internal Server Error', $response->body);
    }

    public function testEveryResponseCarriesSecurityHeaders(): void
    {
        foreach (['/', '/missing'] as $path) {
            $headers = $this->application()->handle(new Request('GET', $path))->headers;

            self::assertStringContainsString("default-src 'none'", $headers['Content-Security-Policy']);
            self::assertSame('nosniff', $headers['X-Content-Type-Options']);
            self::assertSame('DENY', $headers['X-Frame-Options']);
        }
    }

    private function application(): Application
    {
        return new Application(new Menu(require dirname(__DIR__, 2) . '/backend/config/pages.php'), $this->renderer());
    }

    private function renderer(): TemplateRenderer
    {
        return new TemplateRenderer(dirname(__DIR__, 2) . '/frontend/templates');
    }
}
