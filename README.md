# Fakturovo API Client for PHP

Official PHP wrapper for the **Fakturovo public REST API**.

> **Full API reference (Swagger / OpenAPI):**
> [https://api.fakturovo.sk/docs/api](https://api.fakturovo.sk/docs/api)
>
> Every method in this client maps 1 : 1 to an endpoint described in the
> documentation above – check it for request / response schemas, required fields
> and error codes.

---

## Requirements

- PHP **8.0+**
- [Composer](https://getcomposer.org)

## Installation

```bash
composer require fakturovo/api-client
```

---

## Quick start

```php
use Fakturovo\ApiClient\ApiClient;
use Fakturovo\ApiClient\Auth\BasicAuthProvider;
use Fakturovo\ApiClient\ClientOptions;
use Fakturovo\ApiClient\Domain\ValueObject\Uri;

// 1. Create the client — URI is required (no silent default)
$api = new ApiClient(
    new BasicAuthProvider('<YOUR_API_TOKEN>'),
    new ClientOptions(uri: Uri::FAKTUROVO_SK()),              // or Uri::FAKTUROVO_SK_STAGING()
);

// 2. Call any repository method
$response = $api->invoiceRepository->createInvoice([
    'payment_form' => 'bank_transfer',
    'customer' => ['name' => 'Firma s.r.o.'],
    'items' => [
        ['name' => 'Služba', 'quantity' => 1, 'price' => 100.00, 'vat_amount' => 20],
    ],
]);

// 3. Work with the response
$response->getStatusCode(); // e.g. 201
$response->getBody();       // decoded JSON as associative array
$response->isSuccessful();  // true when HTTP 2xx
```

### Authentication via environment variable

```bash
export FAKTUROVO_API_TOKEN="your-token-here"
```

```php
use Fakturovo\ApiClient\Auth\EnvAuthProvider;

$api = new ApiClient(
    new EnvAuthProvider(),
    new ClientOptions(uri: Uri::FAKTUROVO_SK()),
);
// EnvAuthProvider reads the FAKTUROVO_API_TOKEN environment variable.
```

---

## `ClientOptions`

`ClientOptions` is a required second argument to `ApiClient`. It groups all transport-level configuration in one typed object.

```php
use Fakturovo\ApiClient\ClientOptions;
use Fakturovo\ApiClient\Domain\ValueObject\Uri;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('fakturovo');
$logger->pushHandler(new StreamHandler('php://stderr'));

$options = new ClientOptions(
    uri: Uri::FAKTUROVO_SK_STAGING(),  // required — no default to prevent accidental prod calls
    timeoutSeconds: 15,              // HTTP request timeout (default 30)
    maxRetries: 5,                   // max retries on HTTP 429 (default 3)
    logger: $logger,                 // PSR-3 logger, optional
);
```

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `uri` | `Uri` | — (required) | Target server. `Uri::FAKTUROVO_SK()` or `Uri::FAKTUROVO_SK_STAGING()` |
| `timeoutSeconds` | `int` | `30` | HTTP request timeout in seconds |
| `maxRetries` | `int` | `3` | Maximum retries when rate-limited (HTTP 429) |
| `logger` | `?LoggerInterface` | `null` | PSR-3 logger for request/response diagnostics |

### Retry behaviour

When the API responds with **HTTP 429 Too Many Requests**, the client reads the `Retry-After` response header, sleeps for the specified number of seconds, and retries. Retries continue up to `maxRetries` before propagating the response. All retry events are logged at `INFO` level if a logger is configured.

---

## Available repositories & methods

Base URL: `https://api.fakturovo.sk/api/public` (production) / `https://staging-api.fakturovo.sk/api/public` (staging)

Each method returns a `DomainResponseInterface` (`getStatusCode()`, `getBody()`, `isSuccessful()`).

### Account / connection check – `$api->accountRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `me()` | GET | `/v1/me` |

Verifies the API token and returns the branch, token info and effective scopes. Works with any
valid token regardless of its scopes — ideal for a "Verify connection" button.

```php
$response = $api->accountRepository->me();

if ($response->isSuccessful()) {
    $me = $response->getBody()['data'];
    echo $me['branch']['name'];          // e.g. "Eshop A"
    echo $me['token']['test_mode'];      // bool
    print_r($me['scopes']);              // ['invoices.read', 'invoices.write', ...]
}
```

### Customers – `$api->customerRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getCustomers(CustomersListQuery $query)` | GET | `/v1/customers` |
| `getCustomer(int $id)` | GET | `/v1/customers/{id}` |
| `createCustomer(array $data)` | POST | `/v1/customers` |
| `updateCustomer(int $id, array $data)` | PATCH | `/v1/customers/{id}` |
| `deleteCustomer(int $id)` | DELETE | `/v1/customers/{id}` |

### Invoices – `$api->invoiceRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getInvoices(InvoicesListQuery $query)` | GET | `/v1/invoices` |
| `getInvoice(int $id)` | GET | `/v1/invoices/{id}` |
| `createInvoice(array $data)` | POST | `/v1/invoices` |
| `updateInvoice(int $id, array $data)` | PATCH | `/v1/invoices/{id}` |
| `deleteInvoice(int $id)` | DELETE | `/v1/invoices/{id}` |
| `getInvoicePdf(int $id, ?string $language)` | GET | `/v1/invoices/{id}/pdf` |
| `sendInvoiceEmail(int $id, array $data)` | POST | `/v1/invoices/{id}/send-email` |
| `markInvoiceAsSent(int $id)` | POST | `/v1/invoices/{id}/mark-as-sent` |
| `markInvoiceAsPaid(int $id, array $data)` | POST | `/v1/invoices/{id}/mark-as-paid` |

### Proforma invoices – `$api->proformaInvoiceRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getProformaInvoices(ProformaInvoicesListQuery $query)` | GET | `/v1/proforma-invoices` |
| `getProformaInvoice(int $id)` | GET | `/v1/proforma-invoices/{id}` |
| `createProformaInvoice(array $data)` | POST | `/v1/proforma-invoices` |
| `updateProformaInvoice(int $id, array $data)` | PATCH | `/v1/proforma-invoices/{id}` |
| `deleteProformaInvoice(int $id)` | DELETE | `/v1/proforma-invoices/{id}` |
| `getProformaInvoicePdf(int $id, ?string $language)` | GET | `/v1/proforma-invoices/{id}/pdf` |
| `convertToInvoice(int $id)` | POST | `/v1/proforma-invoices/{id}/convert-to-invoice` |

### Credit notes – `$api->creditNoteRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getCreditNotes(CreditNotesListQuery $query)` | GET | `/v1/credit-notes` |
| `getCreditNote(int $id)` | GET | `/v1/credit-notes/{id}` |
| `createCreditNote(array $data)` | POST | `/v1/credit-notes` |
| `updateCreditNote(int $id, array $data)` | PATCH | `/v1/credit-notes/{id}` |
| `deleteCreditNote(int $id)` | DELETE | `/v1/credit-notes/{id}` |
| `getCreditNotePdf(int $id, ?string $language)` | GET | `/v1/credit-notes/{id}/pdf` |

### Expenses – `$api->expenseRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getExpenses(ExpensesListQuery $query)` | GET | `/v1/expenses` |
| `getExpense(int $id)` | GET | `/v1/expenses/{id}` |
| `createExpense(array $data)` | POST | `/v1/expenses` |
| `updateExpense(int $id, array $data)` | PATCH | `/v1/expenses/{id}` |
| `deleteExpense(int $id)` | DELETE | `/v1/expenses/{id}` |

### Bank accounts – `$api->bankAccountRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getBankAccounts()` | GET | `/v1/bank-accounts` |
| `getBankAccount(int $id)` | GET | `/v1/bank-accounts/{id}` |
| `createBankAccount(array $data)` | POST | `/v1/bank-accounts` |
| `updateBankAccount(int $id, array $data)` | PATCH | `/v1/bank-accounts/{id}` |
| `deleteBankAccount(int $id)` | DELETE | `/v1/bank-accounts/{id}` |

### Number series – `$api->numberSeriesRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getNumberSeries(NumberSeriesListQuery $query)` | GET | `/v1/number-series` |

Read-only. Optionally filter by document type: `invoice`, `proforma_invoice`, `credit_note`.

```php
use Fakturovo\ApiClient\Domain\Query\NumberSeriesListQuery;

// All number series for the branch
$api->numberSeriesRepository->getNumberSeries();

// Only invoice number series
$api->numberSeriesRepository->getNumberSeries(new NumberSeriesListQuery(type: 'invoice'));
```

---

## Typed list queries

All list methods accept a typed query DTO instead of a raw `array`. Named constructor arguments make filters self-documenting.

```php
use Fakturovo\ApiClient\Domain\Query\InvoicesListQuery;
use Fakturovo\ApiClient\Domain\Query\CustomersListQuery;
use Fakturovo\ApiClient\Domain\Query\ProformaInvoicesListQuery;
use Fakturovo\ApiClient\Domain\Query\CreditNotesListQuery;
use Fakturovo\ApiClient\Domain\Query\ExpensesListQuery;

// List unpaid invoices from Q1, sorted newest first
$api->invoiceRepository->getInvoices(new InvoicesListQuery(
    paymentStatus: 'unpaid',
    dateFrom: '2025-01-01',
    dateTo: '2025-03-31',
    sort: '-issue_date',
    perPage: 25,
));

// Search customers
$api->customerRepository->getCustomers(new CustomersListQuery(q: 'Firma', perPage: 10));

// Filter expenses
$api->expenseRepository->getExpenses(new ExpensesListQuery(
    dateFrom: '2025-01-01',
    dateTo: '2025-12-31',
    sort: '-issue_date',
));
```

### `InvoicesListQuery` / `ProformaInvoicesListQuery` fields

| Field | Type | Description |
|-------|------|-------------|
| `q` | `?string` | Full-text search |
| `customerId` | `?int` | Filter by customer ID |
| `status` | `?string` | Document status |
| `paymentStatus` | `?string` | `paid`, `unpaid`, `overdue` |
| `dateFrom` | `?string` | Issue date from (`Y-m-d`) |
| `dateTo` | `?string` | Issue date to (`Y-m-d`) |
| `sort` | `?string` | Column, prefix `-` for descending (e.g. `-issue_date`) |
| `perPage` | `?int` | Items per page (max 100) |
| `page` | `?int` | Page number |

### `CreditNotesListQuery` fields

Same as above but without `status`.

### `CustomersListQuery` fields

`q`, `sort`, `perPage`, `page`.

### `ExpensesListQuery` fields

`q`, `customerId`, `dateFrom`, `dateTo`, `sort`, `perPage`, `page`.

---

## Usage examples

### Create a customer

```php
$response = $api->customerRepository->createCustomer([
    'name'  => 'Firma s.r.o.',
    'email' => 'info@firma.sk',
]);

if ($response->isSuccessful()) {
    $customer = $response->getBody()['data'];
    echo 'Created customer #' . $customer['id'];
}
```

### Create an invoice

```php
$response = $api->invoiceRepository->createInvoice([
    'payment_form' => 'bank_transfer',
    'currency'     => 'EUR',
    'customer'     => ['name' => 'Firma s.r.o.'],
    'items'        => [
        ['name' => 'Služba', 'quantity' => 1, 'price' => 100.00, 'vat_amount' => 20],
    ],
]);
```

#### Create a document as already paid

Pass `'paid_default' => true` on **create** to mark the document as fully paid in one call (a
payment for the full amount is recorded and `payment_status` becomes `paid`). Supported on
`createInvoice`, `createProformaInvoice`, `createCreditNote` and `createExpense`:

```php
$api->invoiceRepository->createInvoice([
    'payment_form' => 'bank_transfer',
    'customer'     => ['id' => 123],
    'paid_default' => true,
    'items'        => [['name' => 'Služba', 'quantity' => 1, 'price' => 100.00, 'vat_amount' => 20]],
]);
```

#### Customer resolution

The `customer` object is resolved to an existing customer before a new one is created. Matching
order: explicit `id` → `foreign_source` + `foreign_id` → `identification_number` (IČO) →
`vat_id` / `tax_id`. When no IČO is supplied, it falls back to matching on **`name` + `email`**.
If nothing matches, a new customer is created.

#### Idempotent creation (avoid duplicates on retries)

`createInvoice`, `createProformaInvoice` and `createCreditNote` accept an optional second
argument — an idempotency key (sent as the `Idempotency-Key` header). Retrying with the same
key returns the **original** response instead of creating a second document. Use a stable
identifier such as the e-shop order ID:

```php
$api->invoiceRepository->createInvoice($payload, idempotencyKey: 'woo-order-4815');
// network blip, plugin retries the same call:
$api->invoiceRepository->createInvoice($payload, idempotencyKey: 'woo-order-4815');
// → same invoice, no duplicate; replayed responses carry the `Idempotent-Replayed: true` header.
```

### Per-document appearance override

Invoices, proforma invoices and credit notes accept an optional `appearance` object on
**create** and **update**. It overrides the branch defaults for that single document — useful
when one company runs several e-shops, each with its own logo/colours. Every field is
optional; omit `appearance` entirely to keep the branch defaults.

```php
$api->invoiceRepository->createInvoice([
    'payment_form' => 'bank_transfer',
    'customer'     => ['name' => 'Eshop A s.r.o.'],
    'items'        => [['name' => 'Služba', 'quantity' => 1, 'price' => 100.00, 'vat_amount' => 20]],
    'appearance'   => [
        'logo_url' => 'https://eshop-a.example/logo.png', // fetched & cached server-side
        'color'    => '#0AB1C2',                          // accent colour, #RRGGBB
        'template' => 'style',                            // basic | style | mono
        'language' => 'EN',                               // SK | EN | CZ
    ],
]);
```

| `appearance` field | Type | Notes |
|--------------------|------|-------|
| `logo_url` | string (url) | Downloaded and cached on the server; must be a PNG/JPG/GIF/WEBP/SVG ≤ 5 MB. `null` clears the override. |
| `color` | string | Accent colour as `#RRGGBB`. |
| `template` | string | `basic`, `style`, `mono`. |
| `language` | string | `SK`, `EN`, `CZ` (document language). |

Updating `appearance` via `updateInvoice()` / `updateProformaInvoice()` / `updateCreditNote()`
regenerates the document PDF asynchronously. The response echoes the current values back under
`data.appearance` (with `logo_url` as a short-lived signed URL to the cached image).

### Invoice actions

```php
// Send via email (async job dispatched on the server)
$api->invoiceRepository->sendInvoiceEmail($id, [
    'recipient' => 'client@example.com',
]);

// Mark as sent without email
$api->invoiceRepository->markInvoiceAsSent($id);

// Record full payment
$api->invoiceRepository->markInvoiceAsPaid($id);

// Record partial payment
$api->invoiceRepository->markInvoiceAsPaid($id, [
    'amount'  => 50.00,
    'paid_at' => '2025-04-01',
]);
```

### Convert proforma to invoice

```php
$response = $api->proformaInvoiceRepository->convertToInvoice($proformaId);
$invoice  = $response->getBody()['data']; // new invoice, HTTP 201
```

### Download PDF

```php
// Default language (customer / branch setting)
$pdf = $api->invoiceRepository->getInvoicePdf(123);

// Force English
$pdf = $api->invoiceRepository->getInvoicePdf(123, 'EN'); // SK | EN | CZ
```

#### Permanent PDF links

Every invoice / proforma / credit-note resource exposes its `uuid` plus two **permanent,
unauthenticated** PDF URLs — safe to store or embed in e-mails. They are **always returned**
(already on the `201` create response, before the PDF has finished generating) and never change;
each request redirects to a short-lived signed storage URL:

```php
$invoice = $api->invoiceRepository->createInvoice($payload)->getBody()['data'];

$invoice['uuid'];             // stable document id, e.g. for building URLs yourself
$invoice['pdf_url'];          // inline view  (…/api/invoices/show/{uuid})
$invoice['pdf_download_url']; // force download (…/api/invoices/download/{uuid})
```

> PDF generation is **asynchronous**. The URLs are stable and returned immediately, but they
> return `404` until the document's PDF has been generated (typically a second or two after
> create). Persist the URL and it starts working once generation completes — no need to refetch
> the resource.

### Expenses

Expenses are line-item documents (like invoices): the amount lives on `items`, not on flat
`price` fields. `customer` (a supplier — by `id` or by `name`) and at least one item are
required.

```php
$api->expenseRepository->createExpense([
    'customer'   => ['name' => 'Dodávateľ s.r.o.'],   // or ['id' => 123]
    'issue_date' => '2025-03-15',                      // optional, defaults to today
    'currency'   => 'EUR',                             // optional
    'items'      => [
        ['name' => 'Office supplies', 'quantity' => 1, 'price' => 100.00, 'vat_amount' => 20],
    ],
]);
```

Optional fields: `expense_type` (`invoice` | `receipt`), `delivery_date`, `due_date`,
`category` / `category_id`, `note`, `variable_symbol`, `payment_form`, `paid_default`,
`discount`, `discount_type`, `number`. The response exposes computed `price`,
`price_with_vat`, `total`, `total_with_tax` and the `items` array.

### Bank accounts

`name`, `bank_name`, `swift` and `iban` are required; `code` and `default` are optional.

```php
$api->bankAccountRepository->createBankAccount([
    'name'      => 'Hlavný účet',
    'bank_name' => 'Tatra banka',
    'swift'     => 'TATRSKBX',
    'iban'      => 'SK8909000000000123456789',
    'code'      => '1100',   // optional
    'default'   => true,     // optional
]);
```

---

## Error handling

All API errors throw domain-specific exceptions that extend `FakturvoApiException` (which itself extends `RuntimeException`):

| Exception class | Thrown by |
|-----------------|-----------|
| `CustomerException` | Customer repository |
| `InvoiceException` | Invoice repository |
| `ProformaInvoiceException` | Proforma invoice repository |
| `CreditNoteException` | Credit note repository |
| `ExpenseException` | Expense repository |
| `BankAccountException` | Bank account repository |
| `NumberSeriesException` | Number series repository |
| `AccountException` | Account repository (`me()`) |

```php
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\InvoiceException;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\FakturvoApiException;

try {
    $api->invoiceRepository->getInvoice(999999);
} catch (InvoiceException $e) {
    echo $e->getMessage();
} catch (FakturvoApiException $e) {
    echo $e->getMessage();
}
```

---

## Project structure

```
src/
├── ApiClient.php                          # Main entry point
├── ClientOptions.php                      # Transport config (URI, timeout, retries, logger)
├── Auth/
│   ├── AuthProviderInterface.php
│   ├── BasicAuthProvider.php              # Token from string
│   └── EnvAuthProvider.php               # Token from FAKTUROVO_API_TOKEN env variable
├── Domain/
│   ├── Query/                             # Typed list query DTOs
│   │   ├── InvoicesListQuery.php
│   │   ├── ProformaInvoicesListQuery.php
│   │   ├── CreditNotesListQuery.php
│   │   ├── CustomersListQuery.php
│   │   ├── ExpensesListQuery.php
│   │   └── NumberSeriesListQuery.php
│   ├── Repository/                        # Repository interfaces (contracts)
│   ├── Response/                          # Response DTOs & mappers
│   └── ValueObject/                       # Uri, HttpMethod, AuthHeader
└── Infrastructure/
    ├── Api/
    │   ├── ApiRequestFactory.php          # Builds authenticated PSR-7 requests
    │   └── RetryableHttpClient.php        # 429 retry + PSR-3 logging wrapper
    └── Fakturovo/
        ├── Exception/                     # Domain-specific exceptions
        └── Repository/                    # Concrete repository implementations
```

## License

[MIT](LICENSE)
