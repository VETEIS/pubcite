#!/usr/bin/env php
<?php

/**
 * Fix PHP 8.5+ deprecation warning in Laravel's vendor config file
 * This patches vendor/laravel/framework/config/database.php to use the new Pdo\Mysql::ATTR_SSL_CA constant
 */

$vendorConfigPath = __DIR__ . '/vendor/laravel/framework/config/database.php';

if (!file_exists($vendorConfigPath)) {
    // Laravel config file doesn't exist, nothing to patch
    exit(0);
}

if (PHP_VERSION_ID < 80500) {
    // Not PHP 8.5+, no need to patch
    exit(0);
}

$content = file_get_contents($vendorConfigPath);

// Check if already patched
if (str_contains($content, 'Pdo\\Mysql::ATTR_SSL_CA')) {
    // Already patched
    exit(0);
}

// Replace PDO::MYSQL_ATTR_SSL_CA with Pdo\Mysql::ATTR_SSL_CA
$patched = false;

// Pattern 1: Direct constant usage in array key
$pattern1 = "/PDO::MYSQL_ATTR_SSL_CA\s*=>/";
if (preg_match($pattern1, $content)) {
    $content = preg_replace($pattern1, '\\Pdo\\Mysql::ATTR_SSL_CA =>', $content);
    $patched = true;
}

// Pattern 2: Constant in array_filter or similar contexts
$pattern2 = "/'PDO::MYSQL_ATTR_SSL_CA'/";
if (preg_match($pattern2, $content)) {
    $content = preg_replace($pattern2, "'\\Pdo\\Mysql::ATTR_SSL_CA'", $content);
    $patched = true;
}

if ($patched) {
    file_put_contents($vendorConfigPath, $content);
    echo "✓ Patched Laravel vendor config to fix PHP 8.5+ deprecation warning\n";
} else {
    echo "⚠ Could not find PDO::MYSQL_ATTR_SSL_CA in Laravel config (may already be fixed)\n";
}

exit(0);

