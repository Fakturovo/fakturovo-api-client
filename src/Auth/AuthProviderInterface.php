<?php

namespace Fakturovo\ApiClient\Auth;

use Fakturovo\ApiClient\Domain\ValueObject\AuthHeader;

interface AuthProviderInterface
{
    public function getAuthHeader(): AuthHeader;
}