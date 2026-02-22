<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Vendor\EnterpriseStructure\Support\NamespaceResolver;
use Vendor\EnterpriseStructure\Support\PathResolver;

class CreateContextCommand extends Command
{
    protected $signature = 'make:context {contextName} {--force}';
    protected $description = 'Create Reach Domain Structure inside Source directory';

    public function handle()
    {
        $contextName = trim($this->argument('contextName'));

        if (!preg_match('/^[A-Z][A-Za-z0-9]+$/', $contextName)) {
            $this->error('Bounded Context have to be in StudlyCase');

            return Command::FAILURE;
        }

        $basePath = config('base-domain-structure.paths.src');
        $path = "{$basePath}/{$contextName}";

        if (File::exists($path)) {
            if (!$this->option('force')) {
                $this->error("Context [{$contextName}] already exists. Use --force to overwrite.");

                return Command::FAILURE;
            }
            $this->warn("Removing existing context [{$contextName}]...");
            File::deleteDirectory($path);
        }

        $directories = config('base-domain-structure.structure');

        File::makeDirectory($path, 0755, true);

        $this->createDirectoriesRecursively($path, $directories);

        $this->createFiles($path, $contextName);

        $this->updateOrCreateServiceProvider($contextName);

        $this->info("Bounded Context for [{$contextName}] created successfully.");
        $this->line("Location: {$path}");

        return Command::SUCCESS;
    }

    private function createDirectoriesRecursively(string $basePath, array $directories): void
    {
        foreach ($directories as $name => $children) {
            $dirName = is_int($name) ? $children : $name;
            $dirPath = $basePath . DIRECTORY_SEPARATOR . $dirName;

            File::makeDirectory($dirPath, 0755, true);

            if (!is_int($name) && is_array($children)) {
                $this->createDirectoriesRecursively($dirPath, $children);
            }
        }
    }

    private function createFiles(string $path, string $contextName): void
    {
        $baseNamespace = config('base-domain-structure.namespaces.src') . '\\' . $contextName;

        $stub = File::get(__DIR__ . '/../Stubs/repository.stub');
        $namespace = $baseNamespace . '\\InfrastructureLayer\\Repository';
        $interfaceFqcn = $baseNamespace . '\\DomainLayer\\Repository\\' . $contextName . 'RepositoryInterface';
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ interface }}', '{{ interfaceShortName }}'],
            [$namespace, $contextName . 'Repository', $interfaceFqcn, $contextName . 'RepositoryInterface'],
            $stub
        );
        File::put($path . '/InfrastructureLayer/Repository/' . $contextName . 'Repository.php', $content);

        $stub = File::get(__DIR__ . '/../Stubs/storage.stub');
        $namespace = $baseNamespace . '\\InfrastructureLayer\\Storage';
        $interfaceFqcn = $baseNamespace . '\\DomainLayer\\Storage\\' . $contextName . 'StorageInterface';
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ interface }}', '{{ interfaceShortName }}'],
            [$namespace, $contextName . 'Storage', $interfaceFqcn, $contextName . 'StorageInterface'],
            $stub
        );
        File::put($path . '/InfrastructureLayer/Storage/' . $contextName . 'Storage.php', $content);

        $stub = File::get(__DIR__ . '/../Stubs/repository-interface.stub');
        $namespace = $baseNamespace . '\\DomainLayer\\Repository';
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $contextName . 'RepositoryInterface'],
            $stub
        );
        File::put($path . '/DomainLayer/Repository/' . $contextName . 'RepositoryInterface.php', $content);

        $stub = File::get(__DIR__ . '/../Stubs/storage-interface.stub');
        $namespace = $baseNamespace . '\\DomainLayer\\Storage';
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $contextName . 'StorageInterface'],
            $stub
        );
        File::put($path . '/DomainLayer/Storage/' . $contextName . 'StorageInterface.php', $content);

        $stub = File::get(__DIR__ . '/../Stubs/routes.stub');
        $content = str_replace(
            ['{{ class }}'],
            [$contextName],
            $stub
        );
        File::put($path . '/PresentationLayer/HTTP/V1/routes.php', $content);
    }

    private function updateOrCreateServiceProvider(string $contextName): void
    {
        $basePath = config('base-domain-structure.paths.src');
        $providerPath = $basePath . '/ServiceProvider.php';
        $baseNamespace = config('base-domain-structure.namespaces.src');

        $contexts = collect(File::directories($basePath))
            ->map(fn(string $path) => basename($path))
            ->filter(fn(string $name) => !str_starts_with($name, '.'))
            ->sort()
            ->values()
            ->all();

        $indent = '        ';
        $registerRoutesBody = implode("\n", array_map(
            fn(string $context
            ) => $indent . "Route::middleware(\$this->openMiddleware)->group(__DIR__ . '/{$context}/PresentationLayer/HTTP/V1/routes.php');",
            $contexts
        ));

        $contractsLines = [];
        foreach ($contexts as $context) {
            $domainNs = '\\' . $baseNamespace . '\\' . $context;
            $contractsLines[] = $indent . "\$this->app->bind({$domainNs}\\DomainLayer\\Repository\\{$context}RepositoryInterface::class, {$domainNs}\\InfrastructureLayer\\Repository\\{$context}Repository::class);";
            $contractsLines[] = $indent . "\$this->app->bind({$domainNs}\\DomainLayer\\Storage\\{$context}StorageInterface::class, {$domainNs}\\InfrastructureLayer\\Storage\\{$context}Storage::class);";
        }
        $registerContractsBody = implode("\n", $contractsLines);

        $stub = File::get(__DIR__ . '/../Stubs/service-provider.stub');
        $content = str_replace(
            ['{{ namespace }}', '{{ register_routes_body }}', '{{ register_contracts_body }}'],
            [$baseNamespace, $registerRoutesBody, $registerContractsBody],
            $stub
        );

        File::put($providerPath, $content);
        $this->line("Service provider updated: {$providerPath}");
    }
}
