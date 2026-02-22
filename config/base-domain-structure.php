<?php

$srcDir = env('BASE_DOMAIN_SRC_DIR', 'src');

return [
    'paths' => [
        'src' => base_path($srcDir),
    ],

    'namespaces' => [
        'src' => \Illuminate\Support\Str::studly($srcDir),
    ],

    'structure' => [
        'ApplicationLayer' => [
            'UseCases'
        ],
        'DomainLayer' => [
            'Entities',
            'ValueObjects',
            'Repository',
            'Storage'
        ],
        'InfrastructureLayer' => [
            'Repository',
            'Storage',
        ],
        'PresentationLayer' => [
            'HTTP' => [
                'V1' => [
                    'Controllers',
                    'Requests',
                    'Responders',
                ]
            ]
        ],
    ],

    /*
    | Relative paths (from context root) for make:use-case generated files.
    | Override these to match your preferred structure.
    */
    'use_case_paths' => [
        'use_case'   => 'ApplicationLayer/UseCases',
        'controller' => 'PresentationLayer/HTTP/V1/Controllers',
        'request'    => 'PresentationLayer/HTTP/V1/Requests',
        'responder'  => 'PresentationLayer/HTTP/V1/Responders',
    ],
];
