<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Domain\Repository;

use Fakturovo\ApiClient\Domain\Query\NumberSeriesListQuery;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;

interface NumberSeriesRepositoryInterface
{
    /**
     * List number series for the branch, optionally filtered by document type
     * (invoice, proforma_invoice, credit_note).
     *
     * @see https://api.fakturovo.sk/docs/api#/operations/number-series.getNumberSeries
     */
    public function getNumberSeries(NumberSeriesListQuery $query = new NumberSeriesListQuery()): DomainResponseInterface;

    /**
     * Create a number series.
     *
     * @param array<string, mixed> $data name, type, series_template (required), reset_period (yearly|monthly), offset, default_series
     * @see https://api.fakturovo.sk/docs/api#/operations/numberSeries.createNumberSeries
     */
    public function createNumberSeries(array $data): DomainResponseInterface;

    /**
     * Update a number series (partial).
     *
     * @param array<string, mixed> $data Same fields as createNumberSeries()
     * @see https://api.fakturovo.sk/docs/api#/operations/numberSeries.updateNumberSeries
     */
    public function updateNumberSeries(int $id, array $data): DomainResponseInterface;
}
