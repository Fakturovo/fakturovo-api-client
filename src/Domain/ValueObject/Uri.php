<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\ValueObject;

use InvalidArgumentException;

final class Uri
{
    private const SUPPORTED = [
        'https://api.fakturovo.sk/api/public',
        'https://staging-api.fakturovo.sk/api/public',
    ];

    public string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function FAKTUROVO_SK(): self
    {
        return new self('https://api.fakturovo.sk/api/public');
    }

    public static function FAKTUROVO_SK_STAGING(): self
    {
        return new self('https://staging-api.fakturovo.sk/api/public');
    }

    public static function createFromString(string $value): self
    {
        if (!in_array($value, self::SUPPORTED, true)) {
            throw new InvalidArgumentException("Argument: [$value] is not a supported uri.");
        }

        return new self($value);
    }
}
