<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class NumberSeriesException extends FakturvoApiException
{
    public static function failedToList(\Throwable $previous): self
    {
        return new self("Failed to list number series: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
