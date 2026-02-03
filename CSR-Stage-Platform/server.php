<?php

/**
 * Router for the PHP built-in web server (php artisan serve).
 * Uses __DIR__ so the public path is correct even when getcwd() is wrong.
 */
$publicPath = __DIR__.'/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
