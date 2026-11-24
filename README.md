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

- PHP **8.1+**
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
    new ClientOptions(uri: Uri::FAKTUROVO_SK()),
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
    uri: Uri::FAKTUROVO_SK(),         // required — no default to prevent accidental misconfiguration
    timeoutSeconds: 15,              // HTTP request timeout (default 30)
    maxRetries: 5,                   // max retries on HTTP 429 (default 3)
    logger: $logger,                 // PSR-3 logger, optional
);
```

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `uri` | `Uri` | — (required) | Target server. `Uri::FAKTUROVO_SK()`, or `Uri::custom('https://…/api/public')` for staging / local |
| `timeoutSeconds` | `int` | `30` | HTTP request timeout in seconds |
| `maxRetries` | `int` | `3` | Maximum retries when rate-limited (HTTP 429) |
| `logger` | `?LoggerInterface` | `null` | PSR-3 logger for request/response diagnostics |

### Retry behaviour

When the API responds with **HTTP 429** because of the **per-minute burst limit** (`error: rate_limited`),
the client reads the `Retry-After` header, sleeps and retries — up to `maxRetries` times, then
returns the 429 response. Retry events are logged at `INFO` level if a logger is configured.

The **monthly plan quota** (`error: quota_exceeded`) is never retried: the client throws
`QuotaExceededException` right away (see [Rate limits & plan quotas](#rate-limits--plan-quotas)).

---

## Rate limits & plan quotas

| Limit | Scope | Value | Headers |
|---|---|---|---|
| Burst | per token | 60 requests / minute | `X-RateLimit-Limit`, `X-RateLimit-Remaining` |
| Monthly quota | per account (all tokens) | Free 100 · Lite 500 · Plus 5 000 · Premium unlimited | `X-Quota-Limit`, `X-Quota-Remaining`, `X-Quota-Reset` |

Every response exposes the current state:

```php
$response = $api->invoiceRepository->getInvoices();
$limit = $response->getRateLimit();

$limit->remaining;       // requests left this minute
$limit->quotaRemaining;  // requests left this month (null on unlimited plans)
$limit->quotaResetsAt;   // DateTimeImmutable, 1st of next month
```

```php
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\QuotaExceededException;

try {
    $api->invoiceRepository->getInvoices();
} catch (QuotaExceededException $e) {
    // $e->limit, $e->used, $e->resetsAt — upgrade the plan or wait for the reset
}
```

Plan features are enforced exactly as in the app. Features outside the account's plan answer
`403` with `error: plan_required` and the `feature` key: credit notes (all endpoints); creating,
changing, paying or converting proforma invoices; creating, changing, sending or changing the
status of estimates; creating or changing catalogue items; creating, changing or activating
recurring invoices (reading, deleting and stopping stay allowed, like in the app); creating expenses and number series; creating a second bank account; sending invoices and credit notes by email
(`send-email` or `channels: ['email']`); per-document `appearance` (custom invoice design); and
`linked_documents`. Plan limits answer `402` with `code: limit_reached` — invoices per month
(creating an invoice or converting a proforma). `$api->accountRepository->getSubscription()` shows the plan, its features
and current usage.

---

## Available repositories & methods

Base URL: `https://api.fakturovo.sk/api/public`

Each method returns a `DomainResponseInterface` (`getStatusCode()`, `getBody()`, `isSuccessful()`,
`getHeaderLine()`, `getRateLimit()`). PDF and attachment downloads return
`['content' => <bytes>, 'content_type' => …]` as the body; `DELETE` calls return `204` with an empty body.

### Account / connection check – `$api->accountRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `me()` | GET | `/v1/me` |
| `getSubscription()` | GET | `/v1/subscription` |

`me()` verifies the API token and returns the company, branch (incl. Peppol status), the user who
created the token, token info, effective scopes, the subscription plan and API quota usage. Works with any
valid token regardless of its scopes — ideal for a "Verify connection" button.

```php
$response = $api->accountRepository->me();

if ($response->isSuccessful()) {
    $me = $response->getBody()['data'];
    echo $me['branch']['name'];          // e.g. "Eshop A"
    echo $me['token']['name'];           // e.g. "My integration"
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
| `getCustomerCategories()` | GET | `/v1/customers/categories` |

Customers accept `peppol_participant` (e.g. `0245:2020202020`) to override the Peppol ID that is
otherwise derived from the DIČ.

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
| `sendInvoice(int $id, ?array $channels)` | POST | `/v1/invoices/{id}/send` |
| `sendInvoiceToPeppol(int $id)` | POST | `/v1/invoices/{id}/send-to-peppol` |
| `getInvoicePeppolEvidence(int $id)` | GET | `/v1/invoices/{id}/peppol-evidence` |

### Invoice repeaters – `$api->invoiceRepeaterRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getInvoiceRepeaters(InvoiceRepeatersListQuery $query)` | GET | `/v1/invoice-repeaters` |
| `getInvoiceRepeater(int $id)` | GET | `/v1/invoice-repeaters/{id}` |
| `createInvoiceRepeater(array $data)` | POST | `/v1/invoice-repeaters` |
| `updateInvoiceRepeater(int $id, array $data)` | PATCH | `/v1/invoice-repeaters/{id}` |
| `deleteInvoiceRepeater(int $id)` | DELETE | `/v1/invoice-repeaters/{id}` |
| `stopInvoiceRepeater(int $id)` | POST | `/v1/invoice-repeaters/{id}/stop` |
| `activateInvoiceRepeater(int $id)` | POST | `/v1/invoice-repeaters/{id}/activate` |

A repeater periodically generates a real invoice (e.g. monthly recurring billing). `customer`,
`items`, `payment_form`, `due_days`, `active`, `interval` and `start_at` are required on create;
`updateInvoiceRepeater` accepts the same fields as a partial payload. Invoices generated from a
repeater expose `"type": "automatic"` (vs `"manual"` for everything else) on the invoice
resource — see [Invoice type](#invoice-type) below.

```php
$response = $api->invoiceRepeaterRepository->createInvoiceRepeater([
    'customer'     => ['id' => 123],
    'payment_form' => 'bank_transfer',
    'due_days'     => 14,
    'active'       => true,
    'interval'     => 'monthly',
    'start_at'     => '2026-01-01',
    'items'        => [
        ['name' => 'Hosting', 'quantity' => 1, 'price' => 20.00, 'vat_amount' => 20],
    ],
]);

// Pause / resume without deleting
$api->invoiceRepeaterRepository->stopInvoiceRepeater($id);
$api->invoiceRepeaterRepository->activateInvoiceRepeater($id);
```

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
| `sendCreditNote(int $id, ?array $channels)` | POST | `/v1/credit-notes/{id}/send` |
| `sendCreditNoteEmail(int $id)` | POST | `/v1/credit-notes/{id}/send-email` |
| `sendCreditNoteToPeppol(int $id)` | POST | `/v1/credit-notes/{id}/send-to-peppol` |
| `getCreditNotePeppolEvidence(int $id)` | GET | `/v1/credit-notes/{id}/peppol-evidence` |

`CreditNotesListQuery(direction: 'received')` lists credit notes received from suppliers via Peppol
(issued ones are the default).

### Expenses – `$api->expenseRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getExpenses(ExpensesListQuery $query)` | GET | `/v1/expenses` |
| `getExpense(int $id)` | GET | `/v1/expenses/{id}` |
| `createExpense(array $data)` | POST | `/v1/expenses` |
| `updateExpense(int $id, array $data)` | PATCH | `/v1/expenses/{id}` |
| `deleteExpense(int $id)` | DELETE | `/v1/expenses/{id}` |
| `getExpensePeppolEvidence(int $id)` | GET | `/v1/expenses/{id}/peppol-evidence` |
| `getExpenseCategories()` | GET | `/v1/expenses/categories` |

`ExpensesListQuery(type: 'peppol')` lists expenses received via Peppol (`manual`, `peppol`, `ai`).

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
| `createNumberSeries(array $data)` | POST | `/v1/number-series` |
| `updateNumberSeries(int $id, array $data)` | PATCH | `/v1/number-series/{id}` |

Optionally filter by document type: `invoice`, `proforma_invoice`, `credit_note`, `estimate`.

```php
use Fakturovo\ApiClient\Domain\Query\NumberSeriesListQuery;

// All number series for the branch
$api->numberSeriesRepository->getNumberSeries();

// Only invoice number series
$api->numberSeriesRepository->getNumberSeries(new NumberSeriesListQuery(type: 'invoice'));
```

### Payments – `$api->paymentRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getPayments(PaymentsListQuery $query)` | GET | `/v1/payments` |
| `getPayment(int $id)` | GET | `/v1/payments/{id}` |
| `getDocumentPayments(string $documentType, int $documentId)` | GET | `/v1/{documents}/{id}/payments` |
| `createPayment(string $documentType, int $documentId, array $data)` | POST | `/v1/{documents}/{id}/payments` |
| `updatePayment(int $id, array $data)` | PATCH | `/v1/payments/{id}` |
| `deletePayment(int $id)` | DELETE | `/v1/payments/{id}` |

`$documentType` is one of the `DocumentType` constants: `INVOICE`, `PROFORMA_INVOICE`,
`CREDIT_NOTE`, `EXPENSE`. The document's payment status is recalculated on every change.

### Items (catalogue) – `$api->itemRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getItems(ItemsListQuery $query)` | GET | `/v1/items` |
| `getItem(int $id)` | GET | `/v1/items/{id}` |
| `createItem(array $data)` | POST | `/v1/items` |
| `updateItem(int $id, array $data)` | PATCH | `/v1/items/{id}` |
| `deleteItem(int $id)` | DELETE | `/v1/items/{id}` |
| `getItemUnits()` | GET | `/v1/item-units` |
| `getTaxes()` | GET | `/v1/taxes` |

### Estimates (price quotes) – `$api->estimateRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getEstimates(EstimatesListQuery $query)` | GET | `/v1/estimates` |
| `getEstimate(int $id)` | GET | `/v1/estimates/{id}` |
| `createEstimate(array $data, ?string $idempotencyKey)` | POST | `/v1/estimates` |
| `updateEstimate(int $id, array $data)` | PATCH | `/v1/estimates/{id}` |
| `deleteEstimate(int $id)` | DELETE | `/v1/estimates/{id}` |
| `getEstimatePdf(int $id)` | GET | `/v1/estimates/{id}/pdf` |
| `sendEstimateEmail(int $id)` | POST | `/v1/estimates/{id}/send-email` |
| `changeEstimateStatus(int $id, string $status)` | POST | `/v1/estimates/{id}/change-status` |

### Attachments – `$api->attachmentRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `uploadAttachment(string $contents, string $filename)` | POST | `/v1/attachments` |
| `downloadAttachment(string $uuid)` | GET | `/v1/attachments/{uuid}` |

### Document activity – `$api->activityRepository`

| Method | HTTP | Endpoint |
|--------|------|----------|
| `getDocumentActivities(string $documentType, int $documentId)` | GET | `/v1/{documents}/{id}/activities` |

The activity history of a single document, as shown on the document in the app.

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
| `foreignId` | `?string` | Your own reference set on create (e.g. order number) |
| `foreignSource` | `?string` | Source system of `foreignId` (e.g. `shopify`) |

### `CreditNotesListQuery` fields

Same as above but without `status`.

### `InvoiceRepeatersListQuery` fields

| Field | Type | Description |
|-------|------|-------------|
| `q` | `?string` | Full-text search (customer name/company) |
| `customerId` | `?int` | Filter by customer ID |
| `status` | `?string` | `active` or `inactive` |
| `sort` | `?string` | Column, prefix `-` for descending (e.g. `-start_at`) |
| `perPage` | `?int` | Items per page (max 100) |
| `page` | `?int` | Page number |

### `CustomersListQuery` fields

`q`, `sort`, `perPage`, `page`.

### `ExpensesListQuery` fields

`q`, `customerId`, `dateFrom`, `dateTo`, `sort`, `perPage`, `page`, `foreignId`, `foreignSource`.

Estimates accept `foreignId` and `foreignSource` as well.

### Linking documents to your system

Invoices, proforma invoices, credit notes, estimates and expenses accept `foreign_source` + `foreign_id`
on create and update (not unique — one order can have several documents). They are returned on every
document and in webhook payloads:

```php
$api->invoiceRepository->createInvoice([...$payload, 'foreign_source' => 'shopify', 'foreign_id' => 'ORDER-42']);
$api->invoiceRepository->getInvoices(new InvoicesListQuery(foreignId: 'ORDER-42'));
```

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

#### Payment forms

`payment_form` is required on create and accepts one of:

| Value | Meaning |
|---|---|
| `bank_transfer` | Wire transfer — the only form that prints bank details and a QR payment code on the PDF |
| `cash` | Paid in cash |
| `payment_card` | Card payment |
| `cash_on_delivery` | Cash on delivery (*dobierka* / *dobírka*) — collected by the courier on handover |
| `others` | Anything else |

Any other value is rejected with `422`.

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
        'template' => 'modern',                           // standard | modern | mono
        'language' => 'EN',                               // SK | EN | CZ
    ],
]);
```

| `appearance` field | Type | Notes |
|--------------------|------|-------|
| `logo_url` | string (url) | Downloaded and cached on the server; must be a PNG/JPG/GIF/WEBP/SVG ≤ 5 MB. `null` clears the override. |
| `color` | string | Accent colour as `#RRGGBB`. |
| `template` | string | `standard`, `modern`, `mono`. |
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

### Invoice type

Every invoice exposes `type`: `"automatic"` when it was generated by an [invoice
repeater](#invoice-repeaters--apiinvoicerepeaterrepository) run, `"manual"` otherwise.

```php
$invoice = $api->invoiceRepository->getInvoice($id)->getBody()['data'];

if ($invoice['type'] === 'automatic') {
    // generated by a recurring invoice repeater
}
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

A language override renders a fresh copy on demand (with the ISDOC XML embedded) and never
changes the document's own stored PDF. Received credit notes always return the supplier's PDF.

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

> PDF generation is **asynchronous**. The URLs are stable and returned immediately; persist one
> and it keeps working — no need to refetch the resource. For a document created in the last
> few seconds the request waits up to ~3 seconds for the file; if it is still not ready it answers
> `404`. Every document resource exposes a `pdf_ready` boolean so you can tell without issuing a
> request — cap your retries, a document whose generation failed never becomes ready.

### Sending documents (e-mail & Peppol)

```php
// Like the app: e-mail when the customer has an address, Peppol when the branch has it enabled
$api->invoiceRepository->sendInvoice($id);

// Pick channels explicitly
$api->invoiceRepository->sendInvoice($id, ['peppol']);
$api->invoiceRepository->sendInvoiceToPeppol($id); // strict: 422 if Peppol is not enabled

// Or decide on creation (invoices and credit notes): `send` e-mails, `send_to_peppol` sends via Peppol
$api->invoiceRepository->createInvoice([...$payload, 'send' => true]);                           // e-mail only
$api->invoiceRepository->createInvoice([...$payload, 'send_to_peppol' => true]);                 // Peppol only
$api->invoiceRepository->createInvoice([...$payload, 'send' => true, 'send_to_peppol' => true]); // both
// send_to_peppol: true → 422 when Peppol is not enabled for the branch or the invoice is scheduled

$invoice = $api->invoiceRepository->getInvoice($id)->getBody()['data'];
$invoice['peppol']['status'];        // pending | sent | delivered | failed | action_required | …
$invoice['peppol']['delivered_at'];
$invoice['peppol']['tdd'];           // Slovak CTC reporting result, when applicable
$invoice['is_locked'];               // true once submitted — PATCH/DELETE then return 409

// Delivery receipts (short-lived download URLs)
$api->invoiceRepository->getInvoicePeppolEvidence($id);
```

#### Scheduled invoices

```php
$api->invoiceRepository->createInvoice([
    ...$payload,
    'is_scheduled' => true,
    'issue_date'   => '2026-11-01', // must be in the future
    'send'         => true,         // sent when issued — by e-mail and, like the app, via Peppol
]);

// Scheduled invoices are hidden from lists unless requested
$api->invoiceRepository->getInvoices(new InvoicesListQuery(includeScheduled: true));
```

### Payments

```php
use Fakturovo\ApiClient\Domain\ValueObject\DocumentType;

$api->paymentRepository->createPayment(DocumentType::INVOICE, $invoiceId, [
    'method'  => 'bank_transfer',
    'amount'  => 120.50,
    'paid_at' => '2026-09-01',
]);

$api->paymentRepository->createPayment(DocumentType::INVOICE, $invoiceId, [
    'method'  => 'cash',
    'pay_all' => true, // settle the remaining amount
]);
```

### Attachments

```php
$uuid = $api->attachmentRepository
    ->uploadAttachment(file_get_contents('contract.pdf'), 'contract.pdf')
    ->getBody()['data']['uuid'];

$api->invoiceRepository->createInvoice([...$payload, 'attachment_uuids' => [$uuid]]);

// Expenses carry a single attachment, like in the app (null removes it)
$api->expenseRepository->createExpense([...$expense, 'attachment_uuid' => $uuid]);
$api->expenseRepository->updateExpense($expenseId, ['attachment_uuid' => null]);
```

### Estimates

```php
$api->estimateRepository->createEstimate([
    'customer' => ['name' => 'Firma s.r.o.'],
    'items'    => [['name' => 'Analysis', 'quantity' => 1, 'price' => 300, 'vat_amount' => 20]],
    'groups'   => [[
        'name'  => 'Implementation',
        'items' => [['name' => 'Frontend', 'quantity' => 10, 'price' => 40, 'vat_amount' => 20]],
    ]],
]);

$api->estimateRepository->changeEstimateStatus($id, 'approved');
```

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

### Webhooks

Webhooks are part of the Premium plan. The account owner sets one HTTPS URL in the app (Settings → API → Webhooks) and gets a signing secret
(`whsec_…`). Fakturovo then `POST`s every document change to it: `invoice.created`, `payment.added`,
`invoice.peppol_delivered`, `expense.peppol_received`, … — the full list is in the API documentation.

```php
use Fakturovo\ApiClient\Webhook\InvalidWebhookSignatureException;
use Fakturovo\ApiClient\Webhook\WebhookSignature;

$payload = file_get_contents('php://input');            // raw body, before any JSON decoding
$signature = $_SERVER['HTTP_FAKTUROVO_SIGNATURE'] ?? '';

try {
    $event = WebhookSignature::constructEvent($payload, $signature, getenv('FAKTUROVO_WEBHOOK_SECRET'));
} catch (InvalidWebhookSignatureException) {
    http_response_code(400);
    exit;
}

// $event['id'] is stable across retries — skip ids you already processed
match ($event['type']) {
    'payment.added' => markOrderPaid($event['data']['invoice']['foreign_id']),
    'invoice.peppol_delivered' => markDelivered($event['data']['invoice']['id']),
    default => null,
};

http_response_code(204);
```

- `data` holds the document under its type (`invoice`, `proforma_invoice`, `credit_note`, `estimate`,
  `expense`) in the same shape as the matching `get…()` method, plus `details` (e.g. the payment).
- Answer `2xx` within 10 seconds and do slow work afterwards. Failed deliveries are retried 7 times over
  about a day; `410 Gone` or 20 failed deliveries in a row switch the webhook off.
- Deliveries may arrive out of order and more than once — deduplicate by `$event['id']`.
- `WebhookSignature::verify()` returns a bool if you only need the check; signatures older than 5 minutes
  are rejected.

## Error handling

HTTP error statuses (`4xx`/`5xx`) are returned as responses — check `isSuccessful()` /
`getStatusCode()` and read `getBody()['message']` / `['errors']`. Transport failures and
malformed responses throw domain-specific exceptions extending `FakturvoApiException`
(itself a `RuntimeException`); an exhausted monthly plan quota throws `QuotaExceededException`.

| Exception class | Thrown by |
|-----------------|-----------|
| `CustomerException` | Customer repository |
| `InvoiceException` | Invoice repository |
| `InvoiceRepeaterException` | Invoice repeater repository |
| `ProformaInvoiceException` | Proforma invoice repository |
| `CreditNoteException` | Credit note repository |
| `ExpenseException` | Expense repository |
| `BankAccountException` | Bank account repository |
| `NumberSeriesException` | Number series repository |
| `AccountException` | Account repository (`me()`, `getSubscription()`) |
| `PaymentException` | Payment repository |
| `ItemException` | Item repository |
| `EstimateException` | Estimate repository |
| `AttachmentException` | Attachment repository |
| `ActivityException` | Activity repository |
| `QuotaExceededException` | Any call, when the monthly plan quota is used up |

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
│   │   ├── InvoiceRepeatersListQuery.php
│   │   ├── ProformaInvoicesListQuery.php
│   │   ├── CreditNotesListQuery.php
│   │   ├── CustomersListQuery.php
│   │   ├── ExpensesListQuery.php
│   │   ├── NumberSeriesListQuery.php
│   │   ├── PaymentsListQuery.php
│   │   ├── ItemsListQuery.php
│   │   └── EstimatesListQuery.php
│   ├── Repository/                        # Repository interfaces (contracts)
│   ├── Response/                          # Response DTOs & mappers
│   └── ValueObject/                       # Uri, HttpMethod, AuthHeader, DocumentType, RateLimit
├── Webhook/
│   ├── WebhookSignature.php               # Verify Fakturovo-Signature, decode the event
│   └── InvalidWebhookSignatureException.php
└── Infrastructure/
    ├── Api/
    │   ├── ApiRequestFactory.php          # Builds authenticated PSR-7 requests
    │   └── RetryableHttpClient.php        # burst-limit 429 retry + PSR-3 logging wrapper
    └── Fakturovo/
        ├── Exception/                     # Domain-specific exceptions
        └── Repository/                    # Concrete repository implementations
```

## License

[MIT](LICENSE)
