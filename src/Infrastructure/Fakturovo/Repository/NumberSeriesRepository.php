<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\NumberSeriesListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\NumberSeriesRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\NumberSeriesException;
use Psr\Http\Client\ClientExceptionInterface;

final class NumberSeriesRepository extends AbstractRepository implements NumberSeriesRepositoryInterface
{
    public function getNumberSeries(NumberSeriesListQuery $query = new NumberSeriesListQuery()): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/number-series',
            ['Content-Type' => 'application/json'],
            null,
            $query->toArray(),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw NumberSeriesException::failedToList($e);
        }
    }
}
