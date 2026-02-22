<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Tests;

use DomainDriven\BaseDomainStructure\Providers\BaseDomainStructureServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;


class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BaseDomainStructureServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('base-domain-structure.paths.src', base_path('src'));

        $app['config']->set('base-domain-structure.namespaces.src', 'Src');
    }
}
