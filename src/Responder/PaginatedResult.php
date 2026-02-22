<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Responder;

final readonly class PaginatedResult
{
    public const CURRENT_PAGE = 1;

    public const PER_PAGE = 20;

    public function __construct(
        public array $items,
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $totalRecords,
    ) {}

    public function withProcessedItems(array $processedItems): self
    {
        return new self(
            $processedItems,
            $this->currentPage,
            $this->lastPage,
            $this->perPage,
            $this->totalRecords
        );
    }
}
