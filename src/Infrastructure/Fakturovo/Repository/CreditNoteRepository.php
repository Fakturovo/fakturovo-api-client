<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\CreditNotesListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\CreditNoteRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\CreditNoteException;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;

final class CreditNoteRepository extends AbstractRepository implements CreditNoteRepositoryInterface
{
    public function getCreditNotes(CreditNotesListQuery $query = new CreditNotesListQuery()): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/credit-notes',
            ['Content-Type' => 'application/json'],
            null,
            $query->toArray(),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw CreditNoteException::failedToList($e);
        }
    }

    public function getCreditNote(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/credit-notes/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw CreditNoteException::failedToGet($id, $e);
        }
    }

    public function createCreditNote(array $data, ?string $idempotencyKey = null): DomainResponseInterface
    {
        $headers = ['Content-Type' => 'application/json'];

        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/credit-notes',
            $headers,
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw CreditNoteException::failedToCreate($e);
        }
    }

    public function updateCreditNote(int $id, array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_PATCH,
            '/v1/credit-notes/' . $id,
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw CreditNoteException::failedToUpdate($id, $e);
        }
    }

    public function deleteCreditNote(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_DELETE,
            '/v1/credit-notes/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw CreditNoteException::failedToDelete($id, $e);
        }
    }

    public function getCreditNotePdf(int $id, ?string $language = null): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/credit-notes/' . $id . '/pdf',
            [],
            null,
            $language !== null ? ['language' => $language] : [],
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw CreditNoteException::failedToGetPdf($id, $e);
        }
    }
}
