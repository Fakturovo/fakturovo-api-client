<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\CustomersListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface CustomerRepositoryInterface
{
    /**
     * List customers (paginated).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/customers.getCustomers
     */
    public function getCustomers(CustomersListQuery $query = new CustomersListQuery()): DomainResponseInterface;

    /**
     * Get a single customer by ID.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/customers.getCustomer
     */
    public function getCustomer(int $id): DomainResponseInterface;

    /**
     * Create a new customer.
     *
     * @param array<string, mixed> $data Customer payload (name, customer_type required)
     * @see https://api.fakturovo.sk/docs/api#/operations/customers.createCustomer
     */
    public function createCustomer(array $data): DomainResponseInterface;

    /**
     * Update an existing customer (partial).
     *
     * @param array<string, mixed> $data Partial payload: name, customer_type, email, phone_number,
     *                                   identification_number, tax_id, vat_id, vat_payer, iban,
     *                                   contact_email, contact_phone, web_url, note, street, city,
     *                                   postal_code, country, country_code, currency, document_language,
     *                                   default_invoices_payment_due, foreign_source, foreign_id
     * @see https://api.fakturovo.sk/docs/api#/operations/customers.updateCustomer
     */
    public function updateCustomer(int $id, array $data): DomainResponseInterface;

    /**
     * Delete a customer.
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/customers.deleteCustomer
     */
    public function deleteCustomer(int $id): DomainResponseInterface;
}
