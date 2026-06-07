<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\ValueObject;

class AuthHeader
{
    private function __construct(
        protected string $value,
    ) {
    }

    public static function createFromString(string $value): self
    {
        return new self('Bearer ' . $value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->value;
    }
}
