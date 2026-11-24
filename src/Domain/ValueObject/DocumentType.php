<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Document types used by the payment and activity endpoints.
 */
final class DocumentType
{
    public const INVOICE = 'invoice';
    public const PROFORMA_INVOICE = 'proforma_invoice';
    public const CREDIT_NOTE = 'credit_note';
    public const EXPENSE = 'expense';
    public const ESTIMATE = 'estimate';

    private const PATHS = [
        self::INVOICE => 'invoices',
        self::PROFORMA_INVOICE => 'proforma-invoices',
        self::CREDIT_NOTE => 'credit-notes',
        self::EXPENSE => 'expenses',
        self::ESTIMATE => 'estimates',
    ];

    public static function path(string $documentType): string
    {
        return self::PATHS[$documentType]
            ?? throw new InvalidArgumentException("Argument: [$documentType] is not a supported document type.");
    }
}
