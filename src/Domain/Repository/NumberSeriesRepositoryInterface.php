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
}
