<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Query;

final class CustomersListQuery
{
    public function __construct(
        public ?string $q = null,
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
            'sort' => $this->sort,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ], fn (mixed $v) => $v !== null);
    }
}
