<?php
// Simple router for PHP dev server on Render

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($uri, '/');
if ($path === '') $path = '/';

// 1) If it's a real file (css/js/img), serve it
$full = __DIR__ . $uri;
if ($uri !== '/' && is_file($full)) {
  return false;
}

// 2) Special utility endpoints (kept for debugging)
if ($path === '/healthz.php' || $path === '/which.php' || $path === '/dbtest.php') {
  require __DIR__ . $path;
  exit;
}

// 3) Pretty routes
switch ($path) {
  case '/':
    require __DIR__ . '/index.php';
    break;

  case '/items':
    $_GET['all'] = '1';
    require __DIR__ . '/list.php';
    break;

  case '/add':
    require __DIR__ . '/add.php';
    break;

  case '/public':
    require __DIR__ . '/public_list.php';
    break;

  default:
    // /item/123 → view_item.php?id=123
    if (preg_match('#^/item/(\d+)$#', $path, $m)) {
      $_GET['id'] = $m[1];
      require __DIR__ . '/view_item.php';
      break;
    }

    // Fallback: try a direct php file if it exists (e.g., /categories.php)
    $maybe = __DIR__ . $path;
    if (is_file($maybe) && str_ends_with($maybe, '.php')) {
      require $maybe;
      break;
    }

    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>No route for <code>" . htmlspecialchars($path) . "</code></p>";
}
