<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface ActivityRepositoryInterface
{

    /**
     * Full activity history of one document.
     *
     * @param string $documentType One of the DocumentType constants
     * @see https://api.fakturovo.sk/docs/api#/operations/activities.getDocumentActivities
     */
    public function getDocumentActivities(string $documentType, int $documentId): DomainResponseInterface;
}
