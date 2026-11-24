<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Response\DefaultResponseMapper;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Infrastructure\Api\ApiRequestFactory;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractRepository
{
    protected ApiRequestFactory $apiRequestFactory;
    protected ClientInterface $httpClient;

    public function __construct(
        ApiRequestFactory $apiRequestFactory,
        ClientInterface $httpClient,
    ) {
        $this->apiRequestFactory = $apiRequestFactory;
        $this->httpClient = $httpClient;
    }

    /**
     * @param array<string, mixed>|null $body
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     */
    protected function jsonRequest(string $method, string $uri, ?array $body = null, array $query = [], array $headers = []): RequestInterface
    {
        return $this->apiRequestFactory->createRequest(
            $method,
            $uri,
            $body === null ? $headers : ['Content-Type' => 'application/json', ...$headers],
            $body === null ? null : Stream::create(Utils::jsonEncode($body)),
            $query,
        );
    }

    /**
     * @param callable(\Throwable): \Throwable $onFailure
     */
    protected function send(RequestInterface $request, callable $onFailure): DomainResponseInterface
    {
        try {
            return (new DefaultResponseMapper())->mapToDomainResponse($this->httpClient->sendRequest($request));
        } catch (ClientExceptionInterface|\JsonException $e) {
            throw $onFailure($e);
        }
    }
}
