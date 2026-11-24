<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Webhook;

use RuntimeException;

final class InvalidWebhookSignatureException extends RuntimeException
{
}
