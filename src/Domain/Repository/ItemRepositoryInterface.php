<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\ItemsListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface ItemRepositoryInterface
{
    /**
     * List catalogue items (paginated). Requires a plan with the item catalogue.
     * @see https://api.fakturovo.sk/docs/api#/operations/items.getItems
     */
    public function getItems(ItemsListQuery $query = new ItemsListQuery()): DomainResponseInterface;

    /**
     * Get a catalogue item.
     * @see https://api.fakturovo.sk/docs/api#/operations/items.getItem
     */
    public function getItem(int $id): DomainResponseInterface;

    /**
     * Create a catalogue item.
     *
     * @param array<string, mixed> $data name, price (required), vat_amount, unit_id, description, category, sku
     * @see https://api.fakturovo.sk/docs/api#/operations/items.createItem
     */
    public function createItem(array $data): DomainResponseInterface;

    /**
     * Update a catalogue item (partial).
     *
     * @param array<string, mixed> $data Same fields as createItem()
     * @see https://api.fakturovo.sk/docs/api#/operations/items.updateItem
     */
    public function updateItem(int $id, array $data): DomainResponseInterface;

    /**
     * Delete a catalogue item. Returns 204.
     * @see https://api.fakturovo.sk/docs/api#/operations/items.deleteItem
     */
    public function deleteItem(int $id): DomainResponseInterface;

    /**
     * List units usable as unit_id on items and document lines.
     * @see https://api.fakturovo.sk/docs/api#/operations/items.getItemUnits
     */
    public function getItemUnits(): DomainResponseInterface;

    /**
     * List the branch VAT rates.
     * @see https://api.fakturovo.sk/docs/api#/operations/items.getTaxes
     */
    public function getTaxes(): DomainResponseInterface;
}
