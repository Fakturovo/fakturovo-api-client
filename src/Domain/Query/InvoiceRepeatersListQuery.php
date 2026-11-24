<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Query;

final class InvoiceRepeatersListQuery
{
    public function __construct(
        public ?string $q = null,
        public ?int $customerId = null,
        public ?string $status = null,
        public ?string $sort = null,
        public ?int $perPage = null,
        public ?int $page = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'customer_id' => $this->customerId,
            'status' => $this->status,
            'sort' => $this->sort,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ], fn (mixed $v) => $v !== null);
    }
}
