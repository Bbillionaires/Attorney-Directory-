<?php
error_log("ROUTER hit: " . ($_SERVER['REQUEST_URI'] ?? ''));

// Let PHP dev server serve actual existing files (css/js/img/php)
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $file = __DIR__ . $path;
    if ($path !== '/' && file_exists($file) && is_file($file)) {
        return false;
    }
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Pretty routes
switch ($path) {
    case '':
    case '/':
        require __DIR__ . '/index.php';
        break;
    case '/items':
        require __DIR__ . '/list.php';
        break;
    case '/add':
        require __DIR__ . '/add.php';
        break;
    case '/public':
        require __DIR__ . '/public_list.php';
        break;
    case '/healthz':
        require __DIR__ . '/healthz.php';
        break;
    default:
        // Fallback: allow direct .php files if they exist (back-compat)
        $candidate = __DIR__ . $path;
        if (preg_match('~^/[\w\-/]+\.php$~', $path) && file_exists($candidate)) {
            require $candidate;
            break;
        }
        http_response_code(404);
        echo "Not Found";
}
