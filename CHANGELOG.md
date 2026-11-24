# Changelog

All notable changes to the Fakturovo public API (`/api/public/v1`) and this PHP client are
documented here. Versions follow [Semantic Versioning](https://semver.org/); the client version
matches the API version it targets (the API reports it in the `X-Api-Version` response header).
Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [1.1.0] - 2026-11-24

Additive release — every 1.0.0 integration keeps working on `/v1` without changes, except for
the plan enforcement described under **Changed**.

### Added — API

- **Estimates (cenové ponuky)** — create, list, get, update, delete, PDF, `send-email`,
  `change-status`.
- **Recurring invoices (invoice repeaters)** — create, list, get, update, delete, `activate`, `stop`.
- **Payments** — list and get all payments (`/payments`), update and delete a payment, and per
  document list and add payments on invoices, proforma invoices, credit notes and expenses
  (`/{document}/{id}/payments`).
- **Item catalogue** — items CRUD (`/items`), plus `GET /item-units` and `GET /taxes`.
- **Number series** — create (`POST /number-series`) and update (`PATCH /number-series/{id}`);
  the resource now exposes `reset_period`.
- **Attachments** — `POST /attachments` uploads a file, `GET /attachments/{uuid}` downloads it.
  Attach uploads to invoices, proforma invoices and credit notes with `attachment_uuids`, and to
  expenses with `attachment_uuid`.
- **Document activity** — `GET /{document}/{id}/activities` for invoices, proforma invoices,
  credit notes, estimates and expenses.
- **Categories** — `GET /customers/categories` and `GET /expenses/categories`.
- **Account** — `GET /subscription` (plan, features, limits and usage); `GET /me` now also
  returns the company, its address and Peppol registration, the token's owner and creation and
  last-used times, the plan and the monthly API usage.
- **Sending documents** — `POST /invoices/{id}/send` and `POST /credit-notes/{id}/send` deliver
  over the chosen `channels` (email and/or Peppol). Also added: `POST /credit-notes/{id}/send-email`
  and `send-to-peppol` on invoices and credit notes.
- **Peppol / eFaktúra** — `send_to_peppol` on invoice and credit note create; `peppol` delivery
  state (status, sent/delivered time, error, TDD reporting result) and `is_locked` on invoices and
  credit notes; `GET /{document}/{id}/peppol-evidence` for invoices, credit notes and received
  expenses; `peppol_participant` on customers. Documents already submitted to Peppol are locked:
  update and delete return `409`.
- **Scheduled invoices** — `is_scheduled: true` with a future `issue_date` creates an invoice that
  is issued automatically; lists hide them unless `include_scheduled=true` or `status=scheduled`.
- **External references** — `foreign_source` + `foreign_id` on invoices, proforma invoices,
  credit notes and expenses (create/update), filterable with `?foreign_id=&foreign_source=`.
- **List filters** — credit notes `direction` (`issued` / `received`), expenses `type`
  (`manual` / `peppol` / `ai`).
- **Resource fields** — `payments`, `paid_amount`, `attachments` and `pdf_ready` on documents;
  `type`, `specific_symbol`, `constant_symbol`, `vat_payer` and `source_attachment` on expenses;
  `send_on_issue` on invoices.
- **Idempotency** — `Idempotency-Key` is now also accepted on `POST /estimates`.
- **Webhooks** — the account owner sets one HTTPS URL per company in the app (Settings → API).
  Every document event (created / updated / deleted, payments, emails sent and opened,
  reminders, Peppol and TDD delivery) is POSTed with the public-API resource as `data`, signed
  with `Fakturovo-Signature: t=…,v1=…` (HMAC-SHA256). Failed deliveries are retried up to 8 times
  over about 24 hours.
- **New token scopes** — `estimates.*`, `invoice_repeaters.*`, `payments.*`, `items.*`,
  `attachments.*`, `number_series.write` and `activities.read`. Tokens created without an explicit
  scope list get them automatically. Tokens with an explicit list must be re-issued to use the
  new endpoints.
- **Headers** — every response carries `X-Api-Version`.

### Added — PHP client

- Repositories for every new endpoint: `estimateRepository`, `invoiceRepeaterRepository`,
  `paymentRepository`, `itemRepository`, `attachmentRepository`, `activityRepository`, plus the
  new methods on the existing repositories, with list query DTOs for estimates, invoice
  repeaters, items and payments.
- `DomainResponseInterface::getRateLimit()` and `getHeaderLine()` give the burst and monthly
  quota state of the last response (`RateLimit` value object).
- `QuotaExceededException` — thrown immediately when the monthly plan quota is used up; only
  per-minute burst `429`s are retried, honouring `Retry-After` (at most 60 s).
- `Webhook\WebhookSignature` — verifies the `Fakturovo-Signature` header of incoming webhooks.

### Changed

- **Plan quotas and features are enforced on the API exactly as in the app.**
  - Monthly request quota per account (shared by all its tokens): Free 100, Lite 500,
    Plus 5 000, Premium unlimited. Exceeding it returns `429` with `error: quota_exceeded`.
    `X-Quota-Limit` / `X-Quota-Remaining` / `X-Quota-Reset` report the state.
  - Features the plan does not include return `403` with `error: plan_required` and `feature`.
    These include credit notes, and writes to proforma invoices, estimates, catalogue items and
    recurring invoices (as in the app, existing documents stay readable and deletable), creating expenses, number series and a second bank account, sending by
    email, per-document `appearance`, and `linked_documents`.
  - The monthly invoice limit returns `402` with `code: limit_reached` when creating an invoice or
    converting a proforma.
- The per-minute burst limit (60 requests per token) now answers `429` with
  `error: rate_limited`, `Retry-After` and `X-RateLimit-Limit` / `X-RateLimit-Remaining`.
- PHP client: `composer.json` now declares PHP **8.1** as the minimum. 1.0.0 already needed 8.1
  (it uses `new` in parameter defaults), so this only corrects the constraint. The repository interfaces and
  `DomainResponseInterface` gained methods. If you implement them yourself, for example in test
  doubles, add the new methods.

## [1.0.0]

First public release.

- Customers, invoices, proforma invoices, credit notes, expenses and bank accounts — create, list,
  get, update, delete.
- Invoice actions: `send-email`, `mark-as-sent`, `mark-as-paid`; proforma `convert-to-invoice`.
- Document PDFs (`/pdf`, optional `?language=` override).
- `GET /me`, `GET /number-series`.
- Bearer API tokens with per-resource scopes, `Idempotency-Key` on invoice, proforma and credit
  note creation, 60 requests per minute per token.

[1.1.0]: https://github.com/Fakturovo/fakturovo-api-client/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/Fakturovo/fakturovo-api-client/releases/tag/v1.0.0
