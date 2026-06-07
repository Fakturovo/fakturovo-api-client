<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\AccountRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\AccountException;
use Psr\Http\Client\ClientExceptionInterface;

final class AccountRepository extends AbstractRepository implements AccountRepositoryInterface
{
    public function me(): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/me',
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw AccountException::failedToGetMe($e);
        }
    }
}
