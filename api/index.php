<?php

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