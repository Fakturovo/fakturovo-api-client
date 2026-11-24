<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Query;

final class PaymentsListQuery
{
    public function __construct(
        public ?string $documentType = null,
        public ?int $documentId = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $perPage = null,
        public ?int $page = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'document_type' => $this->documentType,
            'document_id' => $this->documentId,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ], fn (mixed $v) => $v !== null);
    }
}
