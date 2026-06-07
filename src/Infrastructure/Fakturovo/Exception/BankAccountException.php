<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

use Throwable;

class BankAccountException extends FakturvoApiException
{
    public static function failedToList(Throwable $previous): self
    {
        return new self('Failed to list bank accounts.', 0, $previous);
    }

    public static function failedToGet(int $id, Throwable $previous): self
    {
        return new self("Failed to get bank account #{$id}.", 0, $previous);
    }

    public static function failedToCreate(Throwable $previous): self
    {
        return new self('Failed to create bank account.', 0, $previous);
    }

    public static function failedToUpdate(int $id, Throwable $previous): self
    {
        return new self("Failed to update bank account #{$id}.", 0, $previous);
    }

    public static function failedToDelete(int $id, Throwable $previous): self
    {
        return new self("Failed to delete bank account #{$id}.", 0, $previous);
    }
}
