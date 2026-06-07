<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Infrastructure\Api\ApiRequestFactory;
use Psr\Http\Client\ClientInterface;

abstract class AbstractRepository
{
    protected ApiRequestFactory $apiRequestFactory;
    protected ClientInterface $httpClient;

    public function __construct(
        ApiRequestFactory $apiRequestFactory,
        ClientInterface $httpClient,
    ) {
        $this->apiRequestFactory = $apiRequestFactory;
        $this->httpClient = $httpClient;
    }
}
