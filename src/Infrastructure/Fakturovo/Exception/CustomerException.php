<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class CustomerException extends FakturvoApiException
{
    public static function failedToList(\Throwable $previous): self
    {
        return new self("Failed to list customers: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGet(int $id, \Throwable $previous): self
    {
        return new self("Failed to get customer [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToCreate(\Throwable $previous): self
    {
        return new self("Failed to create customer: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToUpdate(int $id, \Throwable $previous): self
    {
        return new self("Failed to update customer [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToDelete(int $id, \Throwable $previous): self
    {
        return new self("Failed to delete customer [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
