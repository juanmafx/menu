<?php

declare(strict_types=1);

use Menu\Application;
use Menu\Http\Request;
use Menu\Navigation\Menu;
use Menu\View\TemplateRenderer;

ini_set('display_errors', '0');

$backendRoot = dirname(__DIR__, 2) . '/backend';
require $backendRoot . '/bootstrap.php';

$application = new Application(
    new Menu(require $backendRoot . '/config/pages.php'),
    new TemplateRenderer(dirname(__DIR__) . '/templates'),
);

$application->handle(Request::fromGlobals($_SERVER))->send();
