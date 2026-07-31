<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = dirname(__DIR__);

$fileMap = [
    '/' => '/index.php',
    '/index' => '/index.php',
    '/index.php' => '/index.php',
    '/about' => '/about.php',
    '/about.php' => '/about.php',
    '/products' => '/products.php',
    '/products.php' => '/products.php',
    '/contact' => '/contact.php',
    '/contact.php' => '/contact.php',
    '/admin' => '/admin.php',
    '/admin.php' => '/admin.php',
    '/ipm' => '/ipm.php',
    '/ipm.php' => '/ipm.php',
];

$target = $fileMap[$uri] ?? '/index.php';
$filePath = $root . $target;

if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once $root . '/index.php';
}
