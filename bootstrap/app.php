<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    });

// Cek kalau jalan di Vercel, storage diarahkan ke /tmp
if (getenv('VERCEL')) {
    $tmpStorage = '/tmp/storage';
    $dirs = [
        $tmpStorage,
        $tmpStorage.'/framework',
        $tmpStorage.'/framework/views',
        $tmpStorage.'/framework/cache',
        $tmpStorage.'/framework/sessions',
        $tmpStorage.'/logs',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    $app->useStoragePath($tmpStorage);
} else {
    $app->useStoragePath(env('APP_STORAGE', dirname(__DIR__) . '/storage'));
}

return $app->create();
