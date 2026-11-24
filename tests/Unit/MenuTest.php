<?php

declare(strict_types=1);

namespace Menu\Tests\Unit;

use InvalidArgumentException;
use Menu\Navigation\Menu;
use Menu\Navigation\Page;
use PHPUnit\Framework\TestCase;

final class MenuTest extends TestCase
{
    public function testKeepsConfiguredOrder(): void
    {
        $menu = new Menu([$this->page('/b', 'b'), $this->page('/a', 'a')]);

        self::assertSame(['/b', '/a'], array_map(static fn (Page $page): string => $page->path, $menu->pages()));
    }

    public function testFindsPageByExactPath(): void
    {
        $page = $this->page('/about', 'about');
        $menu = new Menu([$page]);

        self::assertSame($page, $menu->findByPath('/about'));
        self::assertNull($menu->findByPath('/about/team'));
    }

    public function testMarksOnlyTheCurrentPageActive(): void
    {
        $home = $this->page('/', 'home');
        $about = $this->page('/about', 'about');
        $menu = new Menu([$home, $about]);

        self::assertTrue($menu->isActive($about, '/about'));
        self::assertFalse($menu->isActive($home, '/about'));
    }

    public function testRejectsDuplicatePaths(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Menu([$this->page('/a', 'a'), $this->page('/a', 'b')]);
    }

    public function testRejectsUnnormalizedPagePath(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->page('/about/', 'about');
    }

    public function testRejectsTemplateNameWithTraversal(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->page('/about', '../secret');
    }

    private function page(string $path, string $template): Page
    {
        return new Page($path, 'Label', 'Title', 'Description', $template);
    }
}
