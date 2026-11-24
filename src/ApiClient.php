<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient;

use Fakturovo\ApiClient\Auth\AuthProviderInterface;
use Fakturovo\ApiClient\Domain\Repository\AccountRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\PaymentRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\ItemRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\EstimateRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\AttachmentRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\ActivityRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\BankAccountRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\CreditNoteRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\CustomerRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\ExpenseRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\InvoiceRepeaterRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\InvoiceRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\NumberSeriesRepositoryInterface;
use Fakturovo\ApiClient\Domain\Repository\ProformaInvoiceRepositoryInterface;
use Fakturovo\ApiClient\Infrastructure\Api\ApiRequestFactory;
use Fakturovo\ApiClient\Infrastructure\Api\RetryableHttpClient;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\AccountRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\PaymentRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\ItemRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\EstimateRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\AttachmentRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\ActivityRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\BankAccountRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\CreditNoteRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\CustomerRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\ExpenseRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\InvoiceRepeaterRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\InvoiceRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\NumberSeriesRepository;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository\ProformaInvoiceRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

/**
 * Fakturovo API Client – main entry point.
 *
 * Provides access to all Fakturovo API resources through repository objects.
 *
 * @see https://api.fakturovo.sk/docs/api  Full API documentation
 */
final class ApiClient
{
    public CustomerRepositoryInterface $customerRepository;
    public InvoiceRepositoryInterface $invoiceRepository;
    public InvoiceRepeaterRepositoryInterface $invoiceRepeaterRepository;
    public ProformaInvoiceRepositoryInterface $proformaInvoiceRepository;
    public CreditNoteRepositoryInterface $creditNoteRepository;
    public ExpenseRepositoryInterface $expenseRepository;
    public BankAccountRepositoryInterface $bankAccountRepository;
    public NumberSeriesRepositoryInterface $numberSeriesRepository;
    public AccountRepositoryInterface $accountRepository;
    public PaymentRepositoryInterface $paymentRepository;
    public ItemRepositoryInterface $itemRepository;
    public EstimateRepositoryInterface $estimateRepository;
    public AttachmentRepositoryInterface $attachmentRepository;
    public ActivityRepositoryInterface $activityRepository;

    public function __construct(
        AuthProviderInterface $authProvider,
        ClientOptions $options,
    ) {
        $httpClient = new RetryableHttpClient(
            new Client(['timeout' => $options->timeoutSeconds]),
            $options->maxRetries,
            $options->logger,
        );

        $apiRequestFactory = new ApiRequestFactory(
            new HttpFactory(),
            $options->uri,
            $authProvider,
        );

        $this->customerRepository = new CustomerRepository($apiRequestFactory, $httpClient);
        $this->invoiceRepository = new InvoiceRepository($apiRequestFactory, $httpClient);
        $this->invoiceRepeaterRepository = new InvoiceRepeaterRepository($apiRequestFactory, $httpClient);
        $this->proformaInvoiceRepository = new ProformaInvoiceRepository($apiRequestFactory, $httpClient);
        $this->creditNoteRepository = new CreditNoteRepository($apiRequestFactory, $httpClient);
        $this->expenseRepository = new ExpenseRepository($apiRequestFactory, $httpClient);
        $this->bankAccountRepository = new BankAccountRepository($apiRequestFactory, $httpClient);
        $this->numberSeriesRepository = new NumberSeriesRepository($apiRequestFactory, $httpClient);
        $this->accountRepository = new AccountRepository($apiRequestFactory, $httpClient);
        $this->paymentRepository = new PaymentRepository($apiRequestFactory, $httpClient);
        $this->itemRepository = new ItemRepository($apiRequestFactory, $httpClient);
        $this->estimateRepository = new EstimateRepository($apiRequestFactory, $httpClient);
        $this->attachmentRepository = new AttachmentRepository($apiRequestFactory, $httpClient);
        $this->activityRepository = new ActivityRepository($apiRequestFactory, $httpClient);
    }
}
