<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\ProformaInvoicesListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface ProformaInvoiceRepositoryInterface
{
    /**
     * List proforma invoices (paginated).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/proformaInvoices.getProformaInvoices
     */
    public function getProformaInvoices(ProformaInvoicesListQuery $query = new ProformaInvoicesListQuery()): DomainResponseInterface;

    /**
     * Get a single proforma invoice by ID.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/proformaInvoices.getProformaInvoice
     */
    public function getProformaInvoice(int $id): DomainResponseInterface;

    /**
     * Create a new proforma invoice.
     *
     * @param array<string, mixed> $data Proforma invoice payload (payment_form, customer, items required)
     * @param string|null $idempotencyKey Optional Idempotency-Key header — retrying with the same
     *                                    key returns the original response instead of creating a duplicate.
     * @see https://api.fakturovo.sk/docs/api#/operations/proformaInvoices.createProformaInvoice
     */
    public function createProformaInvoice(array $data, ?string $idempotencyKey = null): DomainResponseInterface;

    /**
     * Update an existing proforma invoice (metadata fields).
     *
     * @param array<string, mixed> $data Partial payload: variable_symbol, order_number, issue_date,
     *                                   due_date, delivery_date, issued_by, note, payment_form,
     *                                   currency, tax_transfer, paid_deposit, discount, discount_type,
     *                                   payment_status
     * @see https://api.fakturovo.sk/docs/api#/operations/proformaInvoices.updateProformaInvoice
     */
    public function updateProformaInvoice(int $id, array $data): DomainResponseInterface;

    /**
     * Delete a proforma invoice.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/proformaInvoices.deleteProformaInvoice
     */
    public function deleteProformaInvoice(int $id): DomainResponseInterface;

    /**
     * Download proforma invoice PDF.
     *
     * @param string|null $language Optional language override (SK, EN, CZ).
     * @see https://api.fakturovo.sk/docs/api#/operations/proformaInvoices.getProformaInvoicePdf
     */
    public function getProformaInvoicePdf(int $id, ?string $language = null): DomainResponseInterface;

    /**
     * Convert a proforma invoice to a regular invoice.
     * Returns the newly created invoice.
     */
    public function convertToInvoice(int $id): DomainResponseInterface;
}
