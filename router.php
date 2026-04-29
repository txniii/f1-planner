<?php
// Router for PHP built-in server (used on Render.com)
// This makes URLs work without Apache/Nginx

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Map clean URLs to PHP files
$routes = [
    '/'              => '/index.php',
    '/login'         => '/login.php',
    '/login.php'     => '/login.php',
    '/register'      => '/register.php',
    '/register.php'  => '/register.php',
    '/logout'        => '/logout.php',
    '/logout.php'    => '/logout.php',
];

if (isset($routes[$uri])) {
    require __DIR__ . $routes[$uri];
    return true;
}

// Let PHP handle existing files
if (file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    require __DIR__ . $uri;
    return true;
}

// 404
http_response_code(404);
echo '<h1>404 Not Found</h1>';
