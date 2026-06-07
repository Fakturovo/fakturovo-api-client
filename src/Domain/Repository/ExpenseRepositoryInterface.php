<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\ExpensesListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface ExpenseRepositoryInterface
{
    /**
     * List expenses (paginated).
     */
    public function getExpenses(ExpensesListQuery $query = new ExpensesListQuery()): DomainResponseInterface;

    /**
     * Get a single expense by ID.
     */
    public function getExpense(int $id): DomainResponseInterface;

    /**
     * Create a new expense.
     *
     * @param array<string, mixed> $data Expense payload (customer + items[] required; each item: name, quantity, price, vat_amount)
     */
    public function createExpense(array $data): DomainResponseInterface;

    /**
     * Update an existing expense.
     *
     * @param array<string, mixed> $data Partial payload
     */
    public function updateExpense(int $id, array $data): DomainResponseInterface;

    /**
     * Delete an expense.
     */
    public function deleteExpense(int $id): DomainResponseInterface;
}
