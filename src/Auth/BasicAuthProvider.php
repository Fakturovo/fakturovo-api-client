<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Auth;

use Fakturovo\ApiClient\Domain\ValueObject\AuthHeader;

class BasicAuthProvider implements AuthProviderInterface
{
    public function __construct(
        public string $authToken,
    ) {
    }

    public function getAuthHeader(): AuthHeader
    {
        return AuthHeader::createFromString($this->authToken);
    }
}
