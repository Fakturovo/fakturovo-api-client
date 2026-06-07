<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class ProformaInvoiceException extends FakturvoApiException
{
    public static function failedToList(\Throwable $previous): self
    {
        return new self("Failed to list proforma invoices: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGet(int $id, \Throwable $previous): self
    {
        return new self("Failed to get proforma invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToCreate(\Throwable $previous): self
    {
        return new self("Failed to create proforma invoice: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToUpdate(int $id, \Throwable $previous): self
    {
        return new self("Failed to update proforma invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToDelete(int $id, \Throwable $previous): self
    {
        return new self("Failed to delete proforma invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGetPdf(int $id, \Throwable $previous): self
    {
        return new self("Failed to get proforma invoice PDF [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToConvert(int $id, \Throwable $previous): self
    {
        return new self("Failed to convert proforma invoice [{$id}] to invoice: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
