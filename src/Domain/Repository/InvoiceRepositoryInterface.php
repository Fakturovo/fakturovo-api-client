<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\InvoicesListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface InvoiceRepositoryInterface
{
    /**
     * List invoices (paginated).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoices.getInvoices
     */
    public function getInvoices(InvoicesListQuery $query = new InvoicesListQuery()): DomainResponseInterface;

    /**
     * Get a single invoice by ID.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoices.getInvoice
     */
    public function getInvoice(int $id): DomainResponseInterface;

    /**
     * Create a new invoice.
     *
     * @param array<string, mixed> $data Invoice payload (payment_form, customer, items required)
     * @param string|null $idempotencyKey Optional Idempotency-Key header — retrying with the same
     *                                    key returns the original response instead of creating a duplicate.
     * @see https://api.fakturovo.sk/docs/api#/operations/invoices.createInvoice
     */
    public function createInvoice(array $data, ?string $idempotencyKey = null): DomainResponseInterface;

    /**
     * Update an existing invoice (metadata fields).
     *
     * @param array<string, mixed> $data Partial payload: variable_symbol, order_number, issue_date,
     *                                   due_date, delivery_date, issued_by, note, payment_form,
     *                                   currency, tax_transfer, paid_deposit, discount, discount_type,
     *                                   payment_status
     * @see https://api.fakturovo.sk/docs/api#/operations/invoices.updateInvoice
     */
    public function updateInvoice(int $id, array $data): DomainResponseInterface;

    /**
     * Delete an invoice.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoices.deleteInvoice
     */
    public function deleteInvoice(int $id): DomainResponseInterface;

    /**
     * Download invoice PDF.
     *
     * @param string|null $language Optional language override (SK, EN, CZ).
     * @see https://api.fakturovo.sk/docs/api#/operations/invoices.getInvoicePdf
     */
    public function getInvoicePdf(int $id, ?string $language = null): DomainResponseInterface;

    /**
     * Send invoice via email. Dispatches an async email job.
     *
     * @param array<string, mixed> $data Optional: recipient, subject, body
     */
    public function sendInvoiceEmail(int $id, array $data = []): DomainResponseInterface;

    /**
     * Mark invoice as sent (without sending email).
     */
    public function markInvoiceAsSent(int $id): DomainResponseInterface;

    /**
     * Record a payment on an invoice.
     *
     * @param array<string, mixed> $data Optional: amount (omit to pay in full), paid_at (Y-m-d), note
     */
    public function markInvoiceAsPaid(int $id, array $data = []): DomainResponseInterface;
}
