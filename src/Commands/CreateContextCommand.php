<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Vendor\EnterpriseStructure\Support\PathResolver;
use Vendor\EnterpriseStructure\Support\NamespaceResolver;

class CreateContextCommand extends Command
{
    protected $signature = 'make:context {contextName} {--force}';
    protected $description = 'Create Reach Domain Structure inside Source directory';

    public function handle()
    {
        $contextName = trim($this->argument('contextName'));

        if (! preg_match('/^[A-Z][A-Za-z0-9]+$/', $contextName)) {
            $this->error('Bounded Context have to be in StudlyCase');
            return Command::FAILURE;
        }

        $basePath = config('base-domain-structure.paths.src');
        $path = "{$basePath}/{$contextName}";

        if (File::exists($path) && ! $this->option('force')) {
            $this->error("Context [{$contextName}] already exists. Use --force to overwrite.");
            return Command::FAILURE;
        }

        $directories = [
            'ApplicationLayer',
            'DomainLayer' => [
                'Repository',
                'Storage',
                'Entities',
                'ValueObjects'
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
        ];

        File::makeDirectory($path, 0755, true);
        File::put($path . '/.gitkeep', '');

        $this->createDirectoriesRecursively($path, $directories);

        $this->createFiles($path, $contextName);

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
            File::put($dirPath . '/.gitkeep', '');

            if (! is_int($name) && is_array($children)) {
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

        File::put($path, File::get(__DIR__ . '/../Stubs/routes.stub'));
    }
}
