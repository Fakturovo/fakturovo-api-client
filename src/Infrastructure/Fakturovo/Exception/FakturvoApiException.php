<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

use RuntimeException;

/**
 * Base exception for all Fakturovo API errors.
 */
class FakturvoApiException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function failed(string $action, \Throwable $previous): static
    {
        return new static("Failed to {$action}: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
