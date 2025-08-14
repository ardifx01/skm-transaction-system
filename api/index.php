<?php
// api/index.php

// Pastikan path /tmp/storage ada (Vercel writable)
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage,
    "$tmpStorage/framework",
    "$tmpStorage/framework/cache",
    "$tmpStorage/framework/sessions",
    "$tmpStorage/framework/views",
    "$tmpStorage/logs"
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Set environment variable APP_STORAGE kalau belum ada
if (!getenv('APP_STORAGE')) {
    putenv("APP_STORAGE=$tmpStorage");
    $_ENV['APP_STORAGE'] = $tmpStorage;
    $_SERVER['APP_STORAGE'] = $tmpStorage;
}

require __DIR__ . '/../public/index.php';
