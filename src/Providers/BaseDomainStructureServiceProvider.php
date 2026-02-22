<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Providers;

use DomainDriven\BaseDomainStructure\Commands\CreateContextCommand;
use DomainDriven\BaseDomainStructure\Commands\CreateUseCase;
use Illuminate\Support\ServiceProvider;

class BaseDomainStructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/base-domain-structure.php',
            'base-domain-structure'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateContextCommand::class,
                CreateUseCase::class,
            ]);

            $this->publishes([
                __DIR__ . '/../../config/base-domain-structure.php'
                => config_path('base-domain-structure.php'),
            ], 'base-domain-structure-config');
        }
    }
}
