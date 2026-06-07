<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Response;

use Psr\Http\Message\ResponseInterface;

interface ResponseMapperInterface
{
    public function mapToDomainResponse(ResponseInterface $response): DomainResponseInterface;
}
