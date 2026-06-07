<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

use Throwable;

class ExpenseException extends FakturvoApiException
{
    public static function failedToList(Throwable $previous): self
    {
        return new self('Failed to list expenses.', 0, $previous);
    }

    public static function failedToGet(int $id, Throwable $previous): self
    {
        return new self("Failed to get expense #{$id}.", 0, $previous);
    }

    public static function failedToCreate(Throwable $previous): self
    {
        return new self('Failed to create expense.', 0, $previous);
    }

    public static function failedToUpdate(int $id, Throwable $previous): self
    {
        return new self("Failed to update expense #{$id}.", 0, $previous);
    }

    public static function failedToDelete(int $id, Throwable $previous): self
    {
        return new self("Failed to delete expense #{$id}.", 0, $previous);
    }
}
