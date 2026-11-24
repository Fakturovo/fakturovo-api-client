<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Webhook;

final class WebhookSignature
{
    public const HEADER = 'Fakturovo-Signature';
    public const DEFAULT_TOLERANCE_SECONDS = 300;

    private const ALGORITHM = 'sha256';

    public static function verify(
        string $payload,
        string $signatureHeader,
        string $secret,
        int $toleranceSeconds = self::DEFAULT_TOLERANCE_SECONDS,
        ?int $now = null,
    ): bool {
        $parts = self::parseHeader($signatureHeader);

        if (!isset($parts['t'], $parts['v1']) || !ctype_digit($parts['t'])) {
            return false;
        }

        if (abs(($now ?? time()) - (int) $parts['t']) > $toleranceSeconds) {
            return false;
        }

        return hash_equals(hash_hmac(self::ALGORITHM, $parts['t'] . '.' . $payload, $secret), $parts['v1']);
    }

    /**
     * @return array{id: string, type: string, created_at: string, api_version: string, data: array<string, mixed>}
     */
    public static function constructEvent(
        string $payload,
        string $signatureHeader,
        string $secret,
        int $toleranceSeconds = self::DEFAULT_TOLERANCE_SECONDS,
    ): array {
        if (!self::verify($payload, $signatureHeader, $secret, $toleranceSeconds)) {
            throw new InvalidWebhookSignatureException('Invalid or expired Fakturovo webhook signature.');
        }

        return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, string>
     */
    private static function parseHeader(string $header): array
    {
        $parts = [];

        foreach (explode(',', $header) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');
            $parts[$key] = $value;
        }

        return $parts;
    }
}
