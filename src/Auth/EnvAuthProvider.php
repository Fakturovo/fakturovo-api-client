<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Auth;

use Fakturovo\ApiClient\Domain\ValueObject\AuthHeader;
use InvalidArgumentException;

/**
 * Reads the API token from the FAKTUROVO_API_TOKEN environment variable.
 */
class EnvAuthProvider implements AuthProviderInterface
{
    private const ENV_VARIABLE = 'FAKTUROVO_API_TOKEN';

    private string $token;

    public function __construct()
    {
        $value = getenv(self::ENV_VARIABLE);

        if ($value === false || $value === '') {
            throw new InvalidArgumentException(
                'Environment variable [' . self::ENV_VARIABLE . '] is not set or is empty.',
            );
        }

        $this->token = $value;
    }

    public function getAuthHeader(): AuthHeader
    {
        return AuthHeader::createFromString($this->token);
    }
}
