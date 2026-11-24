<?php

declare(strict_types=1);

namespace Menu\Tests\Unit;

use Menu\View\Html;
use PHPUnit\Framework\TestCase;

final class HtmlTest extends TestCase
{
    public function testEscapesMarkupAndQuotes(): void
    {
        self::assertSame('&lt;a href=&quot;x&quot;&gt;&apos;&amp;', Html::escape('<a href="x">\'&'));
    }

    public function testSubstitutesInvalidUtf8(): void
    {
        self::assertStringContainsString("\u{FFFD}", Html::escape("bad\xFFbyte"));
    }
}
