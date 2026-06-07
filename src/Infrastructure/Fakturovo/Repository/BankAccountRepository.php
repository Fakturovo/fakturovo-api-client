<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\BankAccountRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\BankAccountException;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;

final class BankAccountRepository extends AbstractRepository implements BankAccountRepositoryInterface
{
    public function getBankAccounts(): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/bank-accounts',
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw BankAccountException::failedToList($e);
        }
    }

    public function getBankAccount(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_GET,
            '/v1/bank-accounts/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw BankAccountException::failedToGet($id, $e);
        }
    }

    public function createBankAccount(array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_POST,
            '/v1/bank-accounts',
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw BankAccountException::failedToCreate($e);
        }
    }

    public function updateBankAccount(int $id, array $data): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_PATCH,
            '/v1/bank-accounts/' . $id,
            ['Content-Type' => 'application/json'],
            Stream::create(Utils::jsonEncode($data)),
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw BankAccountException::failedToUpdate($id, $e);
        }
    }

    public function deleteBankAccount(int $id): DomainResponseInterface
    {
        $request = $this->apiRequestFactory->createRequest(
            HttpMethod::HTTP_METHOD_DELETE,
            '/v1/bank-accounts/' . $id,
        );

        try {
            return (new DefaultResponseMapper())->mapToDomainResponse(
                $this->httpClient->sendRequest($request),
            );
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw BankAccountException::failedToDelete($id, $e);
        }
    }
}
