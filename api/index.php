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
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

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