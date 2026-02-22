<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Vendor\EnterpriseStructure\Support\PathResolver;
use Vendor\EnterpriseStructure\Support\NamespaceResolver;

class CreateEntityCommand extends Command
{
    protected $signature = 'make:domain-entity {name} {--force}';
    protected $description = 'Create a Entity class inside current domain';

    public function handle()
    {
        [$domain, $class] = explode('.', $this->argument('name'));

        $basePath = config('base-domain-structure.paths.src');
        $path = "{$basePath}/{$domain}/DomainLayer/Entities/{$class}.php";

        if (File::exists($path) && ! $this->option('force')) {
            $this->error("Entity already exists. Use --force to overwrite.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));

        $stub = File::get(__DIR__.'/../Stubs/domain/model.stub');

        $namespace = config('base-domain-structure.namespaces.src')
            ."\\{$domain}\\DomainLayer\\Entities";

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        File::put($path, $content);

        $this->info("Entity created: {$path}");

        return Command::SUCCESS;
    }
}
