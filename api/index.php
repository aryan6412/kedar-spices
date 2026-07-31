<?php
// Vercel Serverless Entrypoint
ini_set('display_errors', '0');

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = dirname(__DIR__);

switch ($uri) {
    case '/about':
    case '/about.php':
        require $root . '/about.php';
        break;

    case '/products':
    case '/products.php':
        require $root . '/products.php';
        break;

    case '/contact':
    case '/contact.php':
        require $root . '/contact.php';
        break;

    case '/admin':
    case '/admin.php':
        require $root . '/admin.php';
        break;

    case '/ipm':
    case '/ipm.php':
        require $root . '/ipm.php';
        break;

    default:
        require $root . '/index.php';
        break;
}
