<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('creates use case', function () {
    $srcPath = config('base-domain-structure.paths.src');
    $userPath = $srcPath . DIRECTORY_SEPARATOR . 'User';

    $this->artisan('make:context User')
        ->assertExitCode(0);

    $this->artisan('make:use-case CheckBalance User')
        ->assertExitCode(0);

    $pathSegments = config('base-domain-structure.use_case_paths', [
        'use_case'   => 'ApplicationLayer/UseCases' . '/CheckBalance.php',
        'controller' => 'PresentationLayer/HTTP/V1/Controllers' . '/CheckBalanceController.php',
        'request'    => 'PresentationLayer/HTTP/V1/Requests' . '/CheckBalanceRequest.php',
        'responder'  => 'PresentationLayer/HTTP/V1/Responders' . '/CheckBalanceResponder.php',
    ]);

    foreach ($pathSegments as $pathSegment => $path) {
        expect(File::exists($userPath . '/' . $path))->toBeTrue();
    }

    afterEach(function () {
        File::deleteDirectory(base_path('src'));
    });
});
