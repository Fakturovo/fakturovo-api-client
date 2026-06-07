<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Api;

use Fakturovo\ApiClient\Auth\AuthProviderInterface;
use Fakturovo\ApiClient\Domain\ValueObject\Uri;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

final class ApiRequestFactory
{
    private RequestFactoryInterface $requestFactory;
    private Uri $serverUri;
    private AuthProviderInterface $authProvider;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        Uri $serverUri,
        AuthProviderInterface $authProvider,
    ) {
        $this->requestFactory = $requestFactory;
        $this->serverUri = $serverUri;
        $this->authProvider = $authProvider;
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, string|int> $queryParams
     */
    public function createRequest(
        string $method,
        string $uri,
        array $headers = [],
        ?StreamInterface $body = null,
        array $queryParams = [],
    ): RequestInterface {
        $fullUri = $this->serverUri->value . $uri;

        if (!empty($queryParams)) {
            $filtered = array_filter($queryParams, fn ($v) => $v !== null && $v !== '');
            if (!empty($filtered)) {
                $fullUri .= '?' . http_build_query($filtered);
            }
        }

        $request = $this->requestFactory
            ->createRequest($method, $fullUri)
            ->withHeader('Authorization', $this->authProvider->getAuthHeader()->toString())
            ->withHeader('Accept', 'application/json');

        if ($body !== null) {
            $request = $request->withBody($body);
        } else {
            $request = $request->withBody(Stream::create(''));
        }

        foreach ($headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        return $request;
    }
}
