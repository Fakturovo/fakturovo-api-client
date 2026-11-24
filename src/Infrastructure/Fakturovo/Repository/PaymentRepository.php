<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\PaymentsListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\PaymentRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\DocumentType;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\PaymentException;

final class PaymentRepository extends AbstractRepository implements PaymentRepositoryInterface
{
    public function getPayments(PaymentsListQuery $query = new PaymentsListQuery()): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/payments', null, $query->toArray()),
            fn (\Throwable $e) => PaymentException::failed('list payments', $e),
        );
    }

    public function getPayment(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/payments/' . $id),
            fn (\Throwable $e) => PaymentException::failed("get payment [{$id}]", $e),
        );
    }

    public function getDocumentPayments(string $documentType, int $documentId): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/' . DocumentType::path($documentType) . '/' . $documentId . '/payments'),
            fn (\Throwable $e) => PaymentException::failed("list payments of {$documentType} [{$documentId}]", $e),
        );
    }

    public function createPayment(string $documentType, int $documentId, array $data): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_POST, '/v1/' . DocumentType::path($documentType) . '/' . $documentId . '/payments', $data, []),
            fn (\Throwable $e) => PaymentException::failed("create payment on {$documentType} [{$documentId}]", $e),
        );
    }

    public function updatePayment(int $id, array $data): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_PATCH, '/v1/payments/' . $id, $data, []),
            fn (\Throwable $e) => PaymentException::failed("update payment [{$id}]", $e),
        );
    }

    public function deletePayment(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_DELETE, '/v1/payments/' . $id),
            fn (\Throwable $e) => PaymentException::failed("delete payment [{$id}]", $e),
        );
    }
}
