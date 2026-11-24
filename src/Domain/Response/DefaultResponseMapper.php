<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\QuotaExceededException;
use Psr\Http\Message\ResponseInterface;

class DefaultResponseMapper implements ResponseMapperInterface
{
    public const QUOTA_EXCEEDED = 'quota_exceeded';

    /**
     * @throws \JsonException
     * @throws QuotaExceededException
     */
    public function mapToDomainResponse(ResponseInterface $response): DefaultDomainResponse
    {
        $content = $response->getBody()->getContents();
        $contentType = $response->getHeaderLine('Content-Type');

        $body = match (true) {
            $content === '' => [],
            $contentType !== '' && !str_contains($contentType, 'json') => [
                'content' => $content,
                'content_type' => $contentType,
            ],
            default => json_decode($content, true, 512, JSON_THROW_ON_ERROR),
        };

        if ($response->getStatusCode() === 429 && ($body['error'] ?? null) === self::QUOTA_EXCEEDED) {
            throw QuotaExceededException::fromBody($body);
        }

        $headers = [];
        foreach (array_keys($response->getHeaders()) as $name) {
            $headers[$name] = $response->getHeaderLine($name);
        }

        return new DefaultDomainResponse($response->getStatusCode(), is_array($body) ? $body : [], $headers);
    }
}
