<?php

declare(strict_types=1);

$publicRoot = realpath(__DIR__ . '/../../frontend/public');
$requestPath = rawurldecode((string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH));
$assetPath = str_contains($requestPath, "\0") ? false : realpath($publicRoot . $requestPath);

if ($assetPath !== false && is_file($assetPath) && str_starts_with($assetPath, $publicRoot . '/assets/')) {
    return false;
}

require $publicRoot . '/index.php';
