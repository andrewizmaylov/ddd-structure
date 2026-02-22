<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateUseCase extends Command
{
    protected $signature = 'make:use-case {name} {context?} {--force}';
    protected $description = 'Create UseCase within a bounded context (ApplicationLayer, Controller, Request)';

    public function handle(): int
    {
        $name = trim($this->argument('name'));
        $contextInput = $this->argument('context') ? trim($this->argument('context')) : null;

        if (!preg_match('/^[A-Z][A-Za-z0-9]+$/', $name)) {
            $this->error('Use case name must be StudlyCase (e.g. CreateOrder, GetOrder).');

            return Command::FAILURE;
        }

        $basePath = config('base-domain-structure.paths.src');
        $baseNamespace = config('base-domain-structure.namespaces.src');

        $context = $contextInput ?? $this->resolveContextFromCwd($basePath);
        if ($context === null) {
            $this->error('Could not determine context. Run from inside a context directory or pass context: make:use-case {name} {context}');

            return Command::FAILURE;
        }

        $contextPath = "{$basePath}/{$context}";
        if (!File::isDirectory($contextPath)) {
            $this->error("Context [{$context}] not found at {$contextPath}. Create it with: php artisan make:context {$context}");

            return Command::FAILURE;
        }

        $useCaseClass = $name . 'UseCase';
        $controllerClass = $name . 'Controller';
        $requestClass = $name . 'Request';
        $responderClass = $name . 'Responder';

        $paths = [
            'use_case' => "{$contextPath}/ApplicationLayer/UseCases/{$useCaseClass}.php",
            'controller' => "{$contextPath}/PresentationLayer/HTTP/V1/Controllers/{$controllerClass}.php",
            'request' => "{$contextPath}/PresentationLayer/HTTP/V1/Requests/{$requestClass}.php",
            'responder' => "{$contextPath}/PresentationLayer/HTTP/V1/Responders/{$responderClass}.php",
        ];

        if (!$this->option('force')) {
            foreach ($paths as $key => $path) {
                if (File::exists($path)) {
                    $this->error("File already exists: {$path}. Use --force to overwrite.");

                    return Command::FAILURE;
                }
            }
        }

        File::ensureDirectoryExists(dirname($paths['use_case']));
        File::ensureDirectoryExists(dirname($paths['controller']));
        File::ensureDirectoryExists(dirname($paths['request']));
        File::ensureDirectoryExists(dirname($paths['responder']));

        $contextNs = $baseNamespace . '\\' . $context;
        $storageInterface = $context . 'StorageInterface';
        $repositoryInterface = $context . 'RepositoryInterface';
        $storageFqcn = $contextNs . '\\DomainLayer\\Storage\\' . $storageInterface;
        $repositoryFqcn = $contextNs . '\\DomainLayer\\Repository\\' . $repositoryInterface;

        $useCaseStub = File::get(__DIR__ . '/../Stubs/use-case.stub');
        $useCaseContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ storage }}',
                '{{ repository }}',
                '{{ storage_fqcn }}',
                '{{ repository_fqcn }}'
            ],
            [
                $contextNs . '\\ApplicationLayer\\UseCases',
                $useCaseClass,
                $storageInterface,
                $repositoryInterface,
                $storageFqcn,
                $repositoryFqcn
            ],
            $useCaseStub
        );
        File::put($paths['use_case'], $useCaseContent);

        $requestNs = $contextNs . '\\PresentationLayer\\HTTP\\V1\\Requests';
        $requestStub = File::get(__DIR__ . '/../Stubs/request.stub');
        $requestContent = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$requestNs, $requestClass],
            $requestStub
        );
        File::put($paths['request'], $requestContent);

        $controllerNs = $contextNs . '\\PresentationLayer\\HTTP\\V1\\Controllers';
        $controllerStub = File::get(__DIR__ . '/../Stubs/controller.stub');
        $controllerContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ request }}',
                '{{ request_fqcn }}',
                '{{ process }}',
                '{{ process_fqcn }}',
                '{{ responder }}',
                '{{ responder_fqcn }}',
            ],
            [
                $controllerNs,
                $controllerClass,
                $requestClass,
                $requestNs . '\\' . $requestClass,
                $useCaseClass,
                $contextNs . '\\ApplicationLayer\\UseCases\\' . $useCaseClass,
                $responderClass,
                $contextNs . '\\PresentationLayer\\HTTP\\V1\\Responders\\' . $responderClass,
            ],
            $controllerStub
        );
        File::put($paths['controller'], $controllerContent);

        $responderNs = $contextNs . '\\PresentationLayer\\HTTP\\V1\\Responders';
        $responderStub = File::get(__DIR__ . '/../Stubs/responder.stub');
        $responderContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
            ],
            [
                $responderNs,
                $responderClass,
            ],
            $responderStub
        );
        File::put($paths['responder'], $responderContent);

        $this->info("Use case [{$name}] created in context [{$context}].");
        $this->line("  UseCase:   {$paths['use_case']}");
        $this->line("  Controller: {$paths['controller']}");
        $this->line("  Request:   {$paths['request']}");
        $this->line("  Responder:   {$paths['responder']}");

        return Command::SUCCESS;
    }

    private function resolveContextFromCwd(string $basePath): ?string
    {
        $basePath = realpath($basePath);
        if ($basePath === false) {
            return null;
        }
        $cwd = realpath(getcwd());
        if ($cwd === false || !str_starts_with($cwd, $basePath)) {
            return null;
        }
        $relative = substr($cwd, strlen($basePath));
        $segments = array_filter(explode(DIRECTORY_SEPARATOR, $relative));

        return $segments[0] ?? null;
    }
}
