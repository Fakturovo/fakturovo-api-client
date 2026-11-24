<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Query;

final class ItemsListQuery
{
    public function __construct(
        public ?string $q = null,
        public ?string $category = null,
        public ?int $unitId = null,
        public ?int $perPage = null,
        public ?int $page = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'category' => $this->category,
            'unit_id' => $this->unitId,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ], fn (mixed $v) => $v !== null);
    }
}
