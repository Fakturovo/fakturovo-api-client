<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

class InvoiceException extends FakturvoApiException
{
    public static function failedToList(\Throwable $previous): self
    {
        return new self("Failed to list invoices: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGet(int $id, \Throwable $previous): self
    {
        return new self("Failed to get invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToCreate(\Throwable $previous): self
    {
        return new self("Failed to create invoice: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToUpdate(int $id, \Throwable $previous): self
    {
        return new self("Failed to update invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToDelete(int $id, \Throwable $previous): self
    {
        return new self("Failed to delete invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToGetPdf(int $id, \Throwable $previous): self
    {
        return new self("Failed to get invoice PDF [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToSendEmail(int $id, \Throwable $previous): self
    {
        return new self("Failed to send email for invoice [{$id}]: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToMarkAsSent(int $id, \Throwable $previous): self
    {
        return new self("Failed to mark invoice [{$id}] as sent: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }

    public static function failedToMarkAsPaid(int $id, \Throwable $previous): self
    {
        return new self("Failed to mark invoice [{$id}] as paid: {$previous->getMessage()}", (int) $previous->getCode(), $previous);
    }
}
