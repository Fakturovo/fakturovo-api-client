<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

use Psr\Http\Message\ResponseInterface;

class DefaultResponseMapper implements ResponseMapperInterface
{
    public function mapToDomainResponse(ResponseInterface $response): DefaultDomainResponse
    {
        return new DefaultDomainResponse($response->getStatusCode(), json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));
    }
}
