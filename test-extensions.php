<?php

echo "Testing PHP Extensions...\n";

$required_extensions = [
    'pdo_mysql',
    'pdo_pgsql',
    'pgsql',
    'mbstring',
    'exif',
    'pcntl',
    'bcmath',
    'gd',
    'zip'
];

$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ {$ext} - OK\n";
    } else {
        echo "❌ {$ext} - MISSING\n";
        $missing_extensions[] = $ext;
    }
}

if (empty($missing_extensions)) {
    echo "\n🎉 All required extensions are installed!\n";
    exit(0);
} else {
    echo "\n❌ Missing extensions: " . implode(', ', $missing_extensions) . "\n";
    exit(1);
} 