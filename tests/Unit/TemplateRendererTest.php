<?php

declare(strict_types=1);

namespace Menu\Tests\Unit;

use InvalidArgumentException;
use Menu\View\TemplateRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class TemplateRendererTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/menu-templates-' . bin2hex(random_bytes(6));
        mkdir($this->root);
        file_put_contents($this->root . '/greeting.php', '<?= $name ?>');
        file_put_contents($this->root . '/broken.php', '<?php echo "partial"; throw new RuntimeException("boom");');
    }

    protected function tearDown(): void
    {
        unlink($this->root . '/greeting.php');
        unlink($this->root . '/broken.php');
        rmdir($this->root);
    }

    public static function invalidNameProvider(): array
    {
        return [
            'parent traversal' => ['../secret'],
            'absolute path' => ['/etc/passwd'],
            'extension' => ['greeting.php'],
            'null byte' => ["greeting\0"],
            'empty' => [''],
        ];
    }

    public function testRendersVariables(): void
    {
        self::assertSame('Ada', (new TemplateRenderer($this->root))->render('greeting', ['name' => 'Ada']));
    }

    #[DataProvider('invalidNameProvider')]
    public function testRejectsInvalidTemplateNames(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new TemplateRenderer($this->root))->render($name);
    }

    public function testFailsOnMissingTemplate(): void
    {
        $this->expectException(RuntimeException::class);

        (new TemplateRenderer($this->root))->render('missing');
    }

    public function testDiscardsPartialOutputWhenTemplateThrows(): void
    {
        $level = ob_get_level();

        try {
            (new TemplateRenderer($this->root))->render('broken');
            self::fail('Expected the template to throw.');
        } catch (RuntimeException $exception) {
            self::assertSame('boom', $exception->getMessage());
        }

        self::assertSame($level, ob_get_level());
    }
}
