<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\EstimatesListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\EstimateRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\EstimateException;

final class EstimateRepository extends AbstractRepository implements EstimateRepositoryInterface
{
    public function getEstimates(EstimatesListQuery $query = new EstimatesListQuery()): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/estimates', null, $query->toArray()),
            fn (\Throwable $e) => EstimateException::failed('list estimates', $e),
        );
    }

    public function getEstimate(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/estimates/' . $id),
            fn (\Throwable $e) => EstimateException::failed("get estimate [{$id}]", $e),
        );
    }

    public function createEstimate(array $data, ?string $idempotencyKey = null): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_POST, '/v1/estimates', $data, [], $idempotencyKey === null ? [] : ['Idempotency-Key' => $idempotencyKey]),
            fn (\Throwable $e) => EstimateException::failed('create estimate', $e),
        );
    }

    public function updateEstimate(int $id, array $data): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_PATCH, '/v1/estimates/' . $id, $data, []),
            fn (\Throwable $e) => EstimateException::failed("update estimate [{$id}]", $e),
        );
    }

    public function deleteEstimate(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_DELETE, '/v1/estimates/' . $id),
            fn (\Throwable $e) => EstimateException::failed("delete estimate [{$id}]", $e),
        );
    }

    public function getEstimatePdf(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/estimates/' . $id . '/pdf'),
            fn (\Throwable $e) => EstimateException::failed("get estimate PDF [{$id}]", $e),
        );
    }

    public function sendEstimateEmail(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_POST, '/v1/estimates/' . $id . '/send-email'),
            fn (\Throwable $e) => EstimateException::failed("send estimate [{$id}]", $e),
        );
    }

    public function changeEstimateStatus(int $id, string $status): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_POST, '/v1/estimates/' . $id . '/change-status', ['status' => $status], []),
            fn (\Throwable $e) => EstimateException::failed("change status of estimate [{$id}]", $e),
        );
    }
}
