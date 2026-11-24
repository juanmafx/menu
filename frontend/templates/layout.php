<?php

declare(strict_types=1);

use Menu\Navigation\Menu;
use Menu\View\Html;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= Html::escape($description) ?>">
    <title><?= Html::escape($title) ?></title>
    <link href="/assets/css/main.css" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <nav aria-label="Primary">
            <ul class="menu">
<?php foreach ($menu->pages() as $page): ?>
<?php $isActive = $menu->isActive($page, $currentPath); ?>
                <li<?= $isActive ? ' class="active"' : '' ?>><a href="<?= Html::escape($page->path) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>><?= Html::escape($page->label) ?></a></li>
<?php endforeach; ?>
            </ul>
        </nav>
    </header>
    <main class="content">
<?= $content ?>
    </main>
</body>
</html>
