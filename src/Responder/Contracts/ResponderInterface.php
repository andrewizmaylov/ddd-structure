<?php

namespace DomainDriven\BaseDomainStructure\Responder\Contracts;

use DomainDriven\BaseDomainStructure\Responder\PaginatedResult;

interface ResponderInterface
{
    public function composeEntity(object $entity): array;

    public function composePaginatedResults(PaginatedResult $paginatedResults): PaginatedResult;

    public function composeFromModel(object $model): object;
}
