<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\ValueObject;

/**
 * Rate-limit state reported by the API on every response.
 *
 * Per-minute burst limit: {@see $limit} / {@see $remaining}.
 * Monthly plan quota: {@see $quotaLimit} / {@see $quotaRemaining} / {@see $quotaResetsAt};
 * all three are null on plans with an unlimited quota.
 */
final class RateLimit
{
    public function __construct(
        public readonly ?int $limit,
        public readonly ?int $remaining,
        public readonly ?int $quotaLimit,
        public readonly ?int $quotaRemaining,
        public readonly ?\DateTimeImmutable $quotaResetsAt,
        public readonly ?int $retryAfterSeconds,
    ) {
    }

    /**
     * @param callable(string): string $headerLine
     */
    public static function fromHeaders(callable $headerLine): self
    {
        $int = static fn (string $name): ?int => $headerLine($name) === '' ? null : (int) $headerLine($name);
        $reset = $int('X-Quota-Reset');

        return new self(
            $int('X-RateLimit-Limit'),
            $int('X-RateLimit-Remaining'),
            $int('X-Quota-Limit'),
            $int('X-Quota-Remaining'),
            $reset === null ? null : (new \DateTimeImmutable())->setTimestamp($reset),
            $int('Retry-After'),
        );
    }
}
