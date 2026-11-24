<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\PaymentsListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface PaymentRepositoryInterface
{
    /**
     * List payments across all documents (paginated, newest first).
     * @see https://api.fakturovo.sk/docs/api#/operations/payments.getPayments
     */
    public function getPayments(PaymentsListQuery $query = new PaymentsListQuery()): DomainResponseInterface;

    /**
     * Get a single payment.
     * @see https://api.fakturovo.sk/docs/api#/operations/payments.getPayment
     */
    public function getPayment(int $id): DomainResponseInterface;

    /**
     * List payments of one document.
     *
     * @param string $documentType One of DocumentType::INVOICE, PROFORMA_INVOICE, CREDIT_NOTE, EXPENSE
     * @see https://api.fakturovo.sk/docs/api#/operations/payments.getDocumentPayments
     */
    public function getDocumentPayments(string $documentType, int $documentId): DomainResponseInterface;

    /**
     * Record a payment on a document. Payment status of the document is recalculated.
     *
     * @param string $documentType One of DocumentType::INVOICE, PROFORMA_INVOICE, CREDIT_NOTE, EXPENSE
     * @param array<string, mixed> $data method (required), amount (required unless pay_all), paid_at (Y-m-d), note, pay_all
     * @see https://api.fakturovo.sk/docs/api#/operations/payments.createDocumentPayment
     */
    public function createPayment(string $documentType, int $documentId, array $data): DomainResponseInterface;

    /**
     * Update a payment (partial).
     *
     * @param array<string, mixed> $data method, amount, paid_at, note
     * @see https://api.fakturovo.sk/docs/api#/operations/payments.updatePayment
     */
    public function updatePayment(int $id, array $data): DomainResponseInterface;

    /**
     * Delete a payment. Returns 204 with an empty body.
     * @see https://api.fakturovo.sk/docs/api#/operations/payments.deletePayment
     */
    public function deletePayment(int $id): DomainResponseInterface;
}
