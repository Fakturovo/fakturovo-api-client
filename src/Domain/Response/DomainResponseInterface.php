<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

use Fakturovo\ApiClient\Domain\ValueObject\RateLimit;

interface DomainResponseInterface
{
    public function getStatusCode(): int;

    /**
     * Decoded JSON body. Binary responses (PDF downloads) are returned as
     * ['content' => string, 'content_type' => string]; empty responses (204) as [].
     */
    public function getBody(): array;

    public function isSuccessful(): bool;

    public function getHeaderLine(string $name): string;

    public function getRateLimit(): RateLimit;
}
