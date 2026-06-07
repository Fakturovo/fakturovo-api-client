<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface AccountRepositoryInterface
{
    /**
     * Verify the API token and return the branch, token and effective scopes.
     * Works with any valid token regardless of scopes — use it for connection checks.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/me.getMe
     */
    public function me(): DomainResponseInterface;
}
