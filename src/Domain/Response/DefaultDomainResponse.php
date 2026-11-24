<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

use Fakturovo\ApiClient\Domain\ValueObject\RateLimit;

class DefaultDomainResponse implements DomainResponseInterface
{
    private int $statusCode;
    private array $body;

    /** @var array<string, string> */
    private array $headers;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(int $statusCode, array $body, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = array_change_key_case($headers, CASE_LOWER);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headers[strtolower($name)] ?? '';
    }

    public function getRateLimit(): RateLimit
    {
        return RateLimit::fromHeaders(fn (string $name): string => $this->getHeaderLine($name));
    }
}
