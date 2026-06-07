<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class CreditNoteException extends FakturvoApiException
{
    public static function failedToList(\Throwable $previous): self
    {
        return new self("Failed to list credit notes: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGet(int $id, \Throwable $previous): self
    {
        return new self("Failed to get credit note [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToCreate(\Throwable $previous): self
    {
        return new self("Failed to create credit note: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToUpdate(int $id, \Throwable $previous): self
    {
        return new self("Failed to update credit note [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToDelete(int $id, \Throwable $previous): self
    {
        return new self("Failed to delete credit note [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGetPdf(int $id, \Throwable $previous): self
    {
        return new self("Failed to get credit note PDF [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
