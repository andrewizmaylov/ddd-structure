<?php

$srcDir = env('BASE_DOMAIN_SRC_DIR', 'src');

return [
    'paths' => [
        'src' => app_path($srcDir),
    ],

    'namespaces' => [
        'src' => 'App\\'.\Illuminate\Support\Str::studly($srcDir),
    ],
];
