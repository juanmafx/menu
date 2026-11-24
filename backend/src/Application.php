<?php

declare(strict_types=1);

namespace Menu;

use Menu\Http\Request;
use Menu\Http\Response;
use Menu\Http\SecurityHeaders;
use Menu\Navigation\Menu;
use Menu\Navigation\Page;
use Menu\View\TemplateRenderer;
use Throwable;

final readonly class Application
{
    private const array ALLOWED_METHODS = ['GET', 'HEAD'];

    public function __construct(
        private Menu $menu,
        private TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $headers = SecurityHeaders::for($request);

        if (!in_array($request->method, self::ALLOWED_METHODS, true)) {
            return new Response(
                405,
                $headers + ['Allow' => implode(', ', self::ALLOWED_METHODS), 'Content-Type' => 'text/plain; charset=utf-8'],
                $request->isHead() ? '' : 'Method Not Allowed',
            );
        }

        try {
            $page = $this->menu->findByPath($request->path);
            $status = $page === null ? 404 : 200;
            $body = $this->renderDocument($page, $request->path);
        } catch (Throwable $exception) {
            error_log(sprintf('Unhandled %s while rendering a page', $exception::class));

            return new Response(
                500,
                $headers + ['Content-Type' => 'text/plain; charset=utf-8'],
                $request->isHead() ? '' : 'Internal Server Error',
            );
        }

        return new Response(
            $status,
            $headers + ['Content-Type' => 'text/html; charset=utf-8'],
            $request->isHead() ? '' : $body,
        );
    }

    private function renderDocument(?Page $page, string $currentPath): string
    {
        $content = $this->renderer->render('pages/' . ($page->template ?? 'not-found'));

        return $this->renderer->render('layout', [
            'title' => $page->title ?? 'Page Not Found',
            'description' => $page->description ?? 'The requested page does not exist.',
            'menu' => $this->menu,
            'currentPath' => $currentPath,
            'content' => $content,
        ]);
    }
}
