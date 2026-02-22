<?php

$srcDir = env('BASE_DOMAIN_SRC_DIR', 'src');

return [
    'paths' => [
        'src' => base_path($srcDir),
    ],

    'namespaces' => [
        'src' => \Illuminate\Support\Str::studly($srcDir),
    ],
];
