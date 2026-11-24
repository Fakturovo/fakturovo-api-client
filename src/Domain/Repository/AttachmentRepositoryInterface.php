<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface AttachmentRepositoryInterface
{
    /**
     * Upload a file (PDF, JPEG, PNG or WebP, max 8 MB). The returned `data.uuid` is passed as
     * `attachment_uuids` when creating or updating an invoice, proforma invoice or credit note.
     *
     * @param string $contents Raw file contents
     * @see https://api.fakturovo.sk/docs/api#/operations/attachments.uploadAttachment
     */
    public function uploadAttachment(string $contents, string $filename): DomainResponseInterface;

    /**
     * Download an attachment (body: ['content' => bytes, 'content_type' => ...]).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/attachments.downloadAttachment
     */
    public function downloadAttachment(string $uuid): DomainResponseInterface;
}
