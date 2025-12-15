<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Suppress PHP 8.5+ deprecation warning for PDO::MYSQL_ATTR_SSL_CA in Laravel's vendor config
// This is a known issue in Laravel 12.x that will be fixed in a future update
if (PHP_VERSION_ID >= 80500) {
    // Temporarily suppress E_DEPRECATED warnings during config loading
    $originalErrorReporting = error_reporting(E_ALL & ~E_DEPRECATED);
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Restore original error reporting after Laravel bootstraps
if (PHP_VERSION_ID >= 80500 && isset($originalErrorReporting)) {
    error_reporting($originalErrorReporting);
}

$app->handleRequest(Request::capture());
