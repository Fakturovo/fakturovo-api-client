<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient;

use Fakturovo\ApiClient\Domain\ValueObject\Uri;
use Psr\Log\LoggerInterface;

final class ClientOptions
{
    public Uri $uri;
    public int $timeoutSeconds;
    public int $maxRetries;
    public ?LoggerInterface $logger;

    public function __construct(
        Uri $uri,
        int $timeoutSeconds = 30,
        int $maxRetries = 3,
        ?LoggerInterface $logger = null,
    ) {
        $this->uri = $uri;
        $this->timeoutSeconds = $timeoutSeconds;
        $this->maxRetries = $maxRetries;
        $this->logger = $logger;
    }
}
