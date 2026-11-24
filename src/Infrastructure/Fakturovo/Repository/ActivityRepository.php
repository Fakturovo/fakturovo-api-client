<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\ActivityRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\DocumentType;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\ActivityException;

final class ActivityRepository extends AbstractRepository implements ActivityRepositoryInterface
{
    public function getDocumentActivities(string $documentType, int $documentId): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/' . DocumentType::path($documentType) . '/' . $documentId . '/activities'),
            fn (\Throwable $e) => ActivityException::failed("list activities of {$documentType} [{$documentId}]", $e),
        );
    }
}
