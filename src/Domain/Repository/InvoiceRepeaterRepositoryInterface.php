<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\InvoiceRepeatersListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface InvoiceRepeaterRepositoryInterface
{
    /**
     * List invoice repeaters (paginated).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.getInvoiceRepeaters
     */
    public function getInvoiceRepeaters(InvoiceRepeatersListQuery $query = new InvoiceRepeatersListQuery()): DomainResponseInterface;

    /**
     * Get a single invoice repeater by ID.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.getInvoiceRepeater
     */
    public function getInvoiceRepeater(int $id): DomainResponseInterface;

    /**
     * Create a new invoice repeater.
     *
     * @param array<string, mixed> $data Repeater payload (customer, items, payment_form, due_days,
     *                                   active, interval, start_at required)
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.createInvoiceRepeater
     */
    public function createInvoiceRepeater(array $data): DomainResponseInterface;

    /**
     * Update an existing invoice repeater (partial payload).
     *
     * @param array<string, mixed> $data Partial payload — same fields as create.
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.updateInvoiceRepeater
     */
    public function updateInvoiceRepeater(int $id, array $data): DomainResponseInterface;

    /**
     * Delete an invoice repeater.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.deleteInvoiceRepeater
     */
    public function deleteInvoiceRepeater(int $id): DomainResponseInterface;

    /**
     * Stop an invoice repeater (sets active = false; no more invoices will be generated).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.stopInvoiceRepeater
     */
    public function stopInvoiceRepeater(int $id): DomainResponseInterface;

    /**
     * Activate an invoice repeater (sets active = true; resumes generating invoices).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/invoice_repeaters.activateInvoiceRepeater
     */
    public function activateInvoiceRepeater(int $id): DomainResponseInterface;
}
