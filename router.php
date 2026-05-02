<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$routes = [
    '/'              => '/index.php',
    '/login'         => '/login.php',
    '/login.php'     => '/login.php',
    '/register'      => '/register.php',
    '/register.php'  => '/register.php',
    '/logout'        => '/logout.php',
    '/logout.php'    => '/logout.php',
    '/dashboard'     => '/dashboard.php',
    '/dashboard.php' => '/dashboard.php',
    '/races'         => '/races.php',
    '/races.php'     => '/races.php',
];

if (isset($routes[$uri])) {
    require __DIR__ . $routes[$uri];
    return true;
}

if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

http_response_code(404);
echo '<h1>404 Not Found</h1>';
