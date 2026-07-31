<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

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
$filePath = dirname(__DIR__) . $target;

if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__DIR__) . '/index.php';
}
