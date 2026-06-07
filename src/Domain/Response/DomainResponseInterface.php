<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

interface DomainResponseInterface
{
    public function getStatusCode(): int;

    public function getBody(): array;

    public function isSuccessful(): bool;
}
