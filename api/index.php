<?php
// Vercel Serverless PHP Router
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

chdir(__DIR__ . '/..');

try {
    switch ($uri) {
        case '/':
        case '/index':
        case '/index.php':
            require __DIR__ . '/../index.php';
            break;

        case '/about':
        case '/about.php':
            require __DIR__ . '/../about.php';
            break;

        case '/products':
        case '/products.php':
            require __DIR__ . '/../products.php';
            break;

        case '/contact':
        case '/contact.php':
            require __DIR__ . '/../contact.php';
            break;

        case '/admin':
        case '/admin.php':
            require __DIR__ . '/../admin.php';
            break;

        case '/ipm':
        case '/ipm.php':
            require __DIR__ . '/../ipm.php';
            break;

        default:
            $file = __DIR__ . '/..' . $uri;
            if (file_exists($file) && !is_dir($file) && substr($file, -4) === '.php') {
                require $file;
            } else {
                require __DIR__ . '/../index.php';
            }
            break;
    }
} catch (\Throwable $err) {
    http_response_code(200);
    echo "<h1>Kedarnath Spices & Herbs</h1>";
    echo "<p>Notice: " . htmlspecialchars($err->getMessage()) . "</p>";
}
