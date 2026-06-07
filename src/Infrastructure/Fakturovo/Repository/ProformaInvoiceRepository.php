<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\ProformaInvoicesListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\ProformaInvoiceRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\ProformaInvoiceException;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;

final class ProformaInvoiceRepository extends AbstractRepository implements ProformaInvoiceRepositoryInterface
{
    public function getProformaInvoices(ProformaInvoicesListQuery $query = new ProformaInvoicesListQuery()): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/proforma-invoices',
            ['Content-Type' => 'application/json'],
            null,
            $query->toArray(),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToList($e);
        }
    }

    public function getProformaInvoice(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/proforma-invoices/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToGet($id, $e);
        }
    }

    public function createProformaInvoice(array $data, ?string $idempotencyKey = null): DomainResponseInterface
    {
        $headers = ['Content-Type' => 'application/json'];

        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/proforma-invoices',
            $headers,
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToCreate($e);
        }
    }

    public function updateProformaInvoice(int $id, array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_PATCH,
            '/v1/proforma-invoices/' . $id,
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToUpdate($id, $e);
        }
    }

    public function deleteProformaInvoice(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_DELETE,
            '/v1/proforma-invoices/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToDelete($id, $e);
        }
    }

    public function getProformaInvoicePdf(int $id, ?string $language = null): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/proforma-invoices/' . $id . '/pdf',
            [],
            null,
            $language !== null ? ['language' => $language] : [],
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToGetPdf($id, $e);
        }
    }

    public function convertToInvoice(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/proforma-invoices/' . $id . '/convert-to-invoice',
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw ProformaInvoiceException::failedToConvert($id, $e);
        }
    }
}
