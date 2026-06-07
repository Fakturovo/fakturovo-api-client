<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\InvoicesListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\InvoiceRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\InvoiceException;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;

final class InvoiceRepository extends AbstractRepository implements InvoiceRepositoryInterface
{
    public function getInvoices(InvoicesListQuery $query = new InvoicesListQuery()): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/invoices',
            ['Content-Type' => 'application/json'],
            null,
            $query->toArray(),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToList($e);
        }
    }

    public function getInvoice(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/invoices/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToGet($id, $e);
        }
    }

    public function createInvoice(array $data, ?string $idempotencyKey = null): DomainResponseInterface
    {
        $headers = ['Content-Type' => 'application/json'];

        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoices',
            $headers,
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToCreate($e);
        }
    }

    public function updateInvoice(int $id, array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_PATCH,
            '/v1/invoices/' . $id,
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToUpdate($id, $e);
        }
    }

    public function deleteInvoice(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_DELETE,
            '/v1/invoices/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToDelete($id, $e);
        }
    }

    public function getInvoicePdf(int $id, ?string $language = null): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/invoices/' . $id . '/pdf',
            [],
            null,
            $language !== null ? ['language' => $language] : [],
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToGetPdf($id, $e);
        }
    }

    public function sendInvoiceEmail(int $id, array $data = []): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoices/' . $id . '/send-email',
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToSendEmail($id, $e);
        }
    }

    public function markInvoiceAsSent(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoices/' . $id . '/mark-as-sent',
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToMarkAsSent($id, $e);
        }
    }

    public function markInvoiceAsPaid(int $id, array $data = []): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/invoices/' . $id . '/mark-as-paid',
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw InvoiceException::failedToMarkAsPaid($id, $e);
        }
    }
}
