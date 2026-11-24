<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Query;

final class EstimatesListQuery
{
    public function __construct(
        public ?string $q = null,
        public ?int $customerId = null,
        public ?string $status = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $perPage = null,
        public ?int $page = null,
        public ?string $foreignId = null,
        public ?string $foreignSource = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'customer_id' => $this->customerId,
            'status' => $this->status,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'per_page' => $this->perPage,
            'page' => $this->page,
            'foreign_id' => $this->foreignId,
            'foreign_source' => $this->foreignSource,
        ], fn (mixed $v) => $v !== null);
    }
}
