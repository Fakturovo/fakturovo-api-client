<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Query;

final class NumberSeriesListQuery
{
    public function __construct(
        public ?string $type = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
        ], fn (mixed $v) => $v !== null);
    }
}
