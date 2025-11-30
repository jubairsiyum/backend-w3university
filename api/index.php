<?php

// Vercel serverless environment - use /tmp for writable storage
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Create necessary directories in /tmp
$directories = [
    '/tmp/storage',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Create empty cache files if they don't exist
$cacheFiles = [
    '/tmp/bootstrap/cache/packages.php' => '<?php return [];',
    '/tmp/bootstrap/cache/services.php' => '<?php return [];',
];

foreach ($cacheFiles as $file => $content) {
    if (!file_exists($file)) {
        file_put_contents($file, $content);
    }
}

// Override Laravel's bootstrap cache path
define('LARAVEL_BOOTSTRAP_CACHE', '/tmp/bootstrap/cache');

// Set the correct document root for Laravel
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';

// Override the script name to appear as if coming from public/index.php
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Ensure proper path handling
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}

// Load the Laravel application
require __DIR__ . '/../public/index.php';