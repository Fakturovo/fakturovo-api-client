<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception;

/**
 * Thrown when the monthly API request quota of the account's subscription plan is used up.
 * Unlike the per-minute burst limit this is not retried automatically — upgrade the plan or
 * wait until {@see $resetsAt}.
 */
class QuotaExceededException extends FakturvoApiException
{
    public function __construct(
        public readonly ?int $limit,
        public readonly ?int $used,
        public readonly ?\DateTimeImmutable $resetsAt,
        string $message = 'Monthly API request quota exceeded.',
    ) {
        parent::__construct($message, 429);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromBody(array $body): self
    {
        $resetsAt = isset($body['resets_at']) && is_string($body['resets_at'])
            ? new \DateTimeImmutable($body['resets_at'])
            : null;

        return new self(
            isset($body['limit']) ? (int) $body['limit'] : null,
            isset($body['used']) ? (int) $body['used'] : null,
            $resetsAt,
            is_string($body['message'] ?? null) ? $body['message'] : 'Monthly API request quota exceeded.',
        );
    }
}
