<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class AccountException extends FakturvoApiException
{
    public static function failedToGetMe(\Throwable $previous): self
    {
        return new self("Failed to verify API token: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
