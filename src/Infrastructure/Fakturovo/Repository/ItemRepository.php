<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Query\ItemsListQuery;
use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\ItemRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\ItemException;

final class ItemRepository extends AbstractRepository implements ItemRepositoryInterface
{
    public function getItems(ItemsListQuery $query = new ItemsListQuery()): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/items', null, $query->toArray()),
            fn (\Throwable $e) => ItemException::failed('list items', $e),
        );
    }

    public function getItem(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/items/' . $id),
            fn (\Throwable $e) => ItemException::failed("get item [{$id}]", $e),
        );
    }

    public function createItem(array $data): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_POST, '/v1/items', $data, []),
            fn (\Throwable $e) => ItemException::failed('create item', $e),
        );
    }

    public function updateItem(int $id, array $data): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_PATCH, '/v1/items/' . $id, $data, []),
            fn (\Throwable $e) => ItemException::failed("update item [{$id}]", $e),
        );
    }

    public function deleteItem(int $id): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_DELETE, '/v1/items/' . $id),
            fn (\Throwable $e) => ItemException::failed("delete item [{$id}]", $e),
        );
    }

    public function getItemUnits(): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/item-units'),
            fn (\Throwable $e) => ItemException::failed('list item units', $e),
        );
    }

    public function getTaxes(): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/taxes'),
            fn (\Throwable $e) => ItemException::failed('list taxes', $e),
        );
    }
}
