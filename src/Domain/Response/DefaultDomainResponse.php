<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

class DefaultDomainResponse implements DomainResponseInterface
{
    private int $statusCode;
    private array $body;

    public function __construct(int $statusCode, array $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
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
}
