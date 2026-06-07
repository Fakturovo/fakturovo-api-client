<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\ValueObject;

final class HttpMethod
{
    public const HTTP_METHOD_GET     = 'GET';
    public const HTTP_METHOD_POST    = 'POST';
    public const HTTP_METHOD_PUT     = 'PUT';
    public const HTTP_METHOD_DELETE  = 'DELETE';
    public const HTTP_METHOD_HEAD    = 'HEAD';
    public const HTTP_METHOD_OPTIONS = 'OPTIONS';
    public const HTTP_METHOD_PATCH   = 'PATCH';
}
