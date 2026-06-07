<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface BankAccountRepositoryInterface
{
    /**
     * List all bank accounts for the branch.
     */
    public function getBankAccounts(): DomainResponseInterface;

    /**
     * Get a single bank account by ID.
     */
    public function getBankAccount(int $id): DomainResponseInterface;

    /**
     * Create a new bank account.
     *
     * @param array<string, mixed> $data Payload (name, bank_name, swift, iban required)
     */
    public function createBankAccount(array $data): DomainResponseInterface;

    /**
     * Update an existing bank account.
     *
     * @param array<string, mixed> $data Partial payload
     */
    public function updateBankAccount(int $id, array $data): DomainResponseInterface;

    /**
     * Delete a bank account.
     */
    public function deleteBankAccount(int $id): DomainResponseInterface;
}
