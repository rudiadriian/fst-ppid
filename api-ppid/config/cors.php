<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Origin dibatasi hanya ke panel admin (be-ppid) lewat env ADMIN_ORIGINS.
    | Jangan pernah dikembalikan ke '*': API ini menerima Bearer token, dan
    | wildcard origin membuat situs mana pun bisa memanggilnya dari browser.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_values(array_filter(
        array_map('trim', explode(',', (string) env('ADMIN_ORIGINS', 'http://localhost:3000')))
    )),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-Requested-With'],

    // Fuse membaca token baru dari header ini, jadi harus di-expose lintas origin.
    'exposed_headers' => ['New-Access-Token'],

    'max_age' => 3600,

    'supports_credentials' => false,

];
