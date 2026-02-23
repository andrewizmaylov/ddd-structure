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
src/Balance/
├── ApplicationLayer/
│   └── UseCases/
│       └── UpdateBalanceUseCase.php
├── DomainLayer/
│   ├── Entities/
│   ├── Repository/
│   │   ├── AccountRepositoryInterface.php
│   │   └── BalanceTransactionRepositoryInterface.php
│   ├── Services/
│   │   ├── BalanceUpdateService.php
│   │   └── CreateTransactionsService.php
│   ├── Storage/
│   │   ├── AccountStorageInterface.php
│   │   └── BalanceTransactionStorageInterface.php
│   └── ValueObjects/
├── InfrastructureLayer/
│   ├── Repository/
│   │   ├── AccountRepository.php
│   │   └── BalanceTransactionRepository.php
│   └── Storage/
│       ├── AccountStorage.php
│       └── BalanceTransactionStorage.php
└── PresentationLayer/
    └── HTTP/V1/
        ├── Controllers/
        │   └── UpdateBalanceController.php
        ├── Requests/
        │   └── UpdateBalanceRequest.php
        ├── Responders/
        │   └── BalanceTransactionResponder.php
        └── routes.php
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

Create UseCase structured folder
```text
php artisan make:use-case UpdateBalance Balance
```

### 🙏 Acknowledgements
Mehul Koradiya - For his foundational work on [laravel-enterprise-structure](https://github.com/mehulkoradiya/laravel-enterprise-structure), which inspired this package ❤️.

### 📄 License
The MIT License (MIT). See LICENSE file for details.
