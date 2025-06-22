<?php
// En config/cache.php

use Illuminate\Support\Str;

return [

    // ¡CORRECCIÓN! Cambia 'CACHE_STORE' a 'CACHE_DRIVER' y el valor por defecto a 'file'.
    'default' => env('CACHE_DRIVER', 'file'),

    // ... el resto del archivo puede quedar como está, pero verifica que la
    // sección 'stores' -> 'redis' esté presente.
    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            // ...
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache', // Esto le dice que use la conexión 'cache' de config/database.php
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
             // ...
        ],

        'octane' => [
            'driver' => 'octane',
        ],
    ],

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];
