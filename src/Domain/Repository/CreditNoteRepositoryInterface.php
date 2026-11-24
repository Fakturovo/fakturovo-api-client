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
     * Download credit note PDF (body: ['content' => bytes, 'content_type' => 'application/pdf']).
     *
     * PDFs are generated asynchronously. For a document created in the last few seconds the API
     * waits up to ~3 seconds for the file; if it is still not ready it answers 404. Check the
     * `pdf_ready` flag on the credit note resource and retry later.
     *
     * @param string|null $language Optional language override (SK, EN, CZ).
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.getCreditNotePdf
     */
    public function getCreditNotePdf(int $id, ?string $language = null): DomainResponseInterface;

    /**
     * Send the credit note the way the app does: by e-mail when the customer has an address and via
     * Peppol when the branch has Peppol enabled. Pass channels to pick explicitly.
     *
     * @param list<string>|null $channels 'email' and/or 'peppol'; null = automatic
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.sendCreditNote
     */
    public function sendCreditNote(int $id, ?array $channels = null): DomainResponseInterface;

    /**
     * E-mail the credit note to the customer.
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.sendCreditNoteEmail
     */
    public function sendCreditNoteEmail(int $id): DomainResponseInterface;

    /**
     * Send the credit note via Peppol only. 422 when Peppol is not enabled, 409 when already submitted or received.
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.sendCreditNoteToPeppol
     */
    public function sendCreditNoteToPeppol(int $id): DomainResponseInterface;

    /**
     * Peppol delivery evidence (receipts) of the credit note.
     * @see https://api.fakturovo.sk/docs/api#/operations/creditNotes.getCreditNotePeppolEvidence
     */
    public function getCreditNotePeppolEvidence(int $id): DomainResponseInterface;
}
