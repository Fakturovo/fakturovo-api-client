<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\InvoiceRepeatersListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\InvoiceRepeaterRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\InvoiceRepeaterException;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;

final class InvoiceRepeaterRepository extends AbstractRepository implements InvoiceRepeaterRepositoryInterface
{
    public function getInvoiceRepeaters(InvoiceRepeatersListQuery $query = new InvoiceRepeatersListQuery()): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/invoice-repeaters',
            ['Content-Type' => 'application/json'],
            null,
            $query->toArray(),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToList($e);
        }
    }

    public function getInvoiceRepeater(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/invoice-repeaters/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToGet($id, $e);
        }
    }

    public function createInvoiceRepeater(array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoice-repeaters',
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToCreate($e);
        }
    }

    public function updateInvoiceRepeater(int $id, array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_PATCH,
            '/v1/invoice-repeaters/' . $id,
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToUpdate($id, $e);
        }
    }

    public function deleteInvoiceRepeater(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_DELETE,
            '/v1/invoice-repeaters/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToDelete($id, $e);
        }
    }

    public function stopInvoiceRepeater(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoice-repeaters/' . $id . '/stop',
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToStop($id, $e);
        }
    }

    public function activateInvoiceRepeater(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoice-repeaters/' . $id . '/activate',
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceRepeaterException::failedToActivate($id, $e);
        }
    }
}
