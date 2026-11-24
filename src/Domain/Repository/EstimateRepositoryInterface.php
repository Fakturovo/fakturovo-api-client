<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\EstimatesListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface EstimateRepositoryInterface
{
    /**
     * List estimates (price quotes), paginated.
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.getEstimates
     */
    public function getEstimates(EstimatesListQuery $query = new EstimatesListQuery()): DomainResponseInterface;

    /**
     * Get an estimate with its items and groups.
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.getEstimate
     */
    public function getEstimate(int $id): DomainResponseInterface;

    /**
     * Create an estimate.
     *
     * @param array<string, mixed> $data customer (required), items, groups[{name, items}], name, description, currency, issue_date, due_date, note, emails, send
     * @param string|null $idempotencyKey Optional Idempotency-Key header
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.createEstimate
     */
    public function createEstimate(array $data, ?string $idempotencyKey = null): DomainResponseInterface;

    /**
     * Update an estimate. Omitted fields keep their value; sending items/groups replaces them.
     *
     * @param array<string, mixed> $data Same fields as createEstimate()
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.updateEstimate
     */
    public function updateEstimate(int $id, array $data): DomainResponseInterface;

    /**
     * Delete an estimate. Returns 204.
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.deleteEstimate
     */
    public function deleteEstimate(int $id): DomainResponseInterface;

    /**
     * Download the estimate PDF (body: ['content' => bytes, 'content_type' => ...]).
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.getEstimatePdf
     */
    public function getEstimatePdf(int $id): DomainResponseInterface;

    /**
     * E-mail the estimate to the customer (queued).
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.sendEstimateEmail
     */
    public function sendEstimateEmail(int $id): DomainResponseInterface;

    /**
     * Change the estimate status (draft, created, waiting, approved, rejected, expired, deleted).
     * @see https://api.fakturovo.sk/docs/api#/operations/estimates.changeEstimateStatus
     */
    public function changeEstimateStatus(int $id, string $status): DomainResponseInterface;
}
