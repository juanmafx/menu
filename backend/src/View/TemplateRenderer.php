<?php

declare(strict_types=1);

namespace Menu\View;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

final readonly class TemplateRenderer
{
    public function __construct(private string $templateRoot)
    {
    }

    public function render(string $template, array $variables = []): string
    {
        if (preg_match('#\A[a-z0-9-]+(/[a-z0-9-]+)*\z#', $template) !== 1) {
            throw new InvalidArgumentException('Template name is invalid.');
        }

        $file = $this->templateRoot . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('Template does not exist.');
        }

        $level = ob_get_level();
        ob_start();

        try {
            (static function (string $__file, array $__variables): void {
                extract($__variables, EXTR_SKIP);
                require $__file;
            })($file, $variables);

            return (string) ob_get_clean();
        } catch (Throwable $exception) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $exception;
        }
    }
}
