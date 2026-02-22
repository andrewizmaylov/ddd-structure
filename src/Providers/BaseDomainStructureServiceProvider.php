<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Providers;

use DomainDriven\BaseDomainStructure\Commands\CreateContextCommand;
use DomainDriven\BaseDomainStructure\Commands\CreateEntityCommand;
use DomainDriven\BaseDomainStructure\Commands\CreateRepositoryCommand;
use DomainDriven\BaseDomainStructure\Commands\CreateStorageCommand;
use DomainDriven\BaseDomainStructure\Commands\CreateUseCaseCommand;
use DomainDriven\BaseDomainStructure\Commands\CreateValueObjectCommand;
use DomainDriven\BaseDomainStructure\Commands\InstallBaseStructureCommand;
use Illuminate\Support\ServiceProvider;

class BaseDomainStructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/base-domain-structure.php',
            'base-domain-structure'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                CreateContextCommand::class,
                CreateEntityCommand::class,
                CreateRepositoryCommand::class,
                CreateStorageCommand::class,
                CreateUseCaseCommand::class,
                CreateValueObjectCommand::class,
                InstallBaseStructureCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../../config/base-domain-structure.php'
                => config_path('base-domain-structure.php'),
            ], 'base-domain-structure-config');
        }
    }
}
