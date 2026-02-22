# Domain Structure for Laravel Project

A template scaffold that implements Domain-Driven Design principles for Laravel, providing a clean, opinionated structure so you can focus on what matters: your business domain.

### 🚀 Installation
```text
composer require andreyizmaylov/base-domain-structure
```
### 🎯 Publish the configuration: *
```text
php artisan vendor:publish --tag=base-domain-structure-config
```
### 🔄 Directory Structure Visualization

Here's a more detailed tree view of your configurable structure:
```text
src/ # Source root (configurable via .env)
|
├── Balance
|    ├── ApplicationLayer/ # Application Layer
|    │ └── UseCases/ # Orchestrate business workflows
|    │
|    ├── DomainLayer/ # Domain Layer (Core business logic)
|    │ ├── Entities/ # Business entities with behavior
|    │ ├── ValueObjects/ # Immutable, comparable objects
|    │ ├── Repository/ # Repository contracts (interfaces)
|    │ └── Storage/ # Storage contracts
|    │
|    ├── InfrastructureLayer/ # Infrastructure Layer
|    │ ├── Repository/ # Concrete repository implementations
|    │ └── Storage/ # Concrete storage implementations
|    │
|    └── PresentationLayer/ # Presentation Layer
|    |  └── HTTP/
|    |   └── V1/ # API versioning
|    |      ├── Controllers/ # Handle HTTP requests
|    |      ├── Requests/ # Validation rules
|    |      ├── Responders/ # Transform responses (JSON/
|    |      └── routes.php
|
├── Company
|
├── Package
|
└── ServiceProvider.php 
```
After publishing, you can modify default domain structure:
```php
    'structure' => [
        'ApplicationLayer',
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
                    'routes.php'
                ]
            ]
        ],
    ],
```


### 📁 Customize Your Source Folder

By default, domains are created in `app/src/`. You can change this via your `.env` file:
```text
BASE_DOMAIN_SRC_DIR=Domain
```

### Commands

Create Context structured folder
```text
php artisan make:context Balance
```

### 🙏 Acknowledgements
Mehul Koradiya - For his foundational work on [laravel-enterprise-structure](https://github.com/mehulkoradiya/laravel-enterprise-structure), which inspired this package ❤️.

### 📄 License
The MIT License (MIT). See LICENSE file for details.
