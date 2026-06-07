<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\CreditNotesListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface CreditNoteRepositoryInterface
{
    /**
     * List credit notes (paginated).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.getCreditNotes
     */
    public function getCreditNotes(CreditNotesListQuery $query = new CreditNotesListQuery()): DomainResponseInterface;

    /**
     * Get a single credit note by ID.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.getCreditNote
     */
    public function getCreditNote(int $id): DomainResponseInterface;

    /**
     * Create a new credit note.
     *
     * @param array<string, mixed> $data Credit note payload (payment_form, customer, items required)
     * @param string|null $idempotencyKey Optional Idempotency-Key header — retrying with the same
     *                                    key returns the original response instead of creating a duplicate.
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.createCreditNote
     */
    public function createCreditNote(array $data, ?string $idempotencyKey = null): DomainResponseInterface;

    /**
     * Update an existing credit note (metadata fields).
     *
     * @param array<string, mixed> $data Partial payload: variable_symbol, order_number, issue_date,
     *                                   due_date, delivery_date, issued_by, note, payment_form,
     *                                   currency, discount, discount_type, payment_status
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.updateCreditNote
     */
    public function updateCreditNote(int $id, array $data): DomainResponseInterface;

    /**
     * Delete a credit note.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.deleteCreditNote
     */
    public function deleteCreditNote(int $id): DomainResponseInterface;

    /**
     * Download credit note PDF.
     *
     * @param string|null $language Optional language override (SK, EN, CZ).
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.getCreditNotePdf
     */
    public function getCreditNotePdf(int $id, ?string $language = null): DomainResponseInterface;
}
