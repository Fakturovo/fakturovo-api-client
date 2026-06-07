<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Api;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class RetryableHttpClient implements ClientInterface
{
    private ClientInterface $inner;
    private int $maxRetries;
    private ?LoggerInterface $logger;

    public function __construct(
        ClientInterface $inner,
        int $maxRetries,
        ?LoggerInterface $logger = null,
    ) {
        $this->inner = $inner;
        $this->maxRetries = $maxRetries;
        $this->logger = $logger;
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->logger !== null) {
            $this->logger->debug('Fakturovo API request', [
                'method' => $request->getMethod(),
                'uri' => (string) $request->getUri(),
            ]);
        }

        $attempt = 0;

        while (true) {
            $response = $this->inner->sendRequest($request);

            if ($response->getStatusCode() !== 429 || $attempt >= $this->maxRetries) {
                if ($response->getStatusCode() >= 400 && $this->logger !== null) {
                    $this->logger->warning('Fakturovo API error response', [
                        'status' => $response->getStatusCode(),
                        'method' => $request->getMethod(),
                        'uri' => (string) $request->getUri(),
                    ]);
                }

                return $response;
            }

            $retryAfter = max(1, (int) ($response->getHeaderLine('Retry-After') ?: 1));
            $attempt++;

            if ($this->logger !== null) {
                $this->logger->info('Fakturovo API rate limited, retrying', [
                    'retry_after_seconds' => $retryAfter,
                    'attempt' => $attempt,
                    'max_retries' => $this->maxRetries,
                ]);
            }

            sleep($retryAfter);
        }
    }
}
