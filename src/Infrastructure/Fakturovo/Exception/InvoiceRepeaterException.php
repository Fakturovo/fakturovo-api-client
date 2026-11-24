<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class InvoiceRepeaterException extends FakturvoApiException
{
    public static function failedToList(\Throwable $previous): self
    {
        return new self("Failed to list invoice repeaters: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGet(int $id, \Throwable $previous): self
    {
        return new self("Failed to get invoice repeater [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToCreate(\Throwable $previous): self
    {
        return new self("Failed to create invoice repeater: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToUpdate(int $id, \Throwable $previous): self
    {
        return new self("Failed to update invoice repeater [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToDelete(int $id, \Throwable $previous): self
    {
        return new self("Failed to delete invoice repeater [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToStop(int $id, \Throwable $previous): self
    {
        return new self("Failed to stop invoice repeater [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToActivate(int $id, \Throwable $previous): self
    {
        return new self("Failed to activate invoice repeater [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
