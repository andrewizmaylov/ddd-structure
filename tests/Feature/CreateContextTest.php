<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('creates context structure', function () {
    $srcPath = config('base-domain-structure.paths.src');
    $userPath = $srcPath . DIRECTORY_SEPARATOR . 'User';

    $this->artisan('make:context User --force')
        ->assertExitCode(0);

    $pathSegments = config('base-domain-structure.use_case_paths', [
        'use_case'   => 'ApplicationLayer/UseCases',
        'controller' => 'PresentationLayer/HTTP/V1/Controllers',
        'request'    => 'PresentationLayer/HTTP/V1/Requests',
        'responder'  => 'PresentationLayer/HTTP/V1/Responders',
    ]);

    foreach ($pathSegments as $pathSegment => $path) {
        expect(File::exists($userPath . '/' . $path))->toBeTrue();
    }

    afterEach(function () {
        File::deleteDirectory(base_path('src'));
    });
});
