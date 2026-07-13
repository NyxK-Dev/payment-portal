# Technology Flow Guide — Payment Portal

**CodeIgniter 3 · PHP 7.3 · MySQL 8 · Stripe**

**Related:** [Technology Guide](technology-guide.md) · [Installation Guide](installation-guide.md) · [Database Design](database-design.md)

---

### 1. Registration & Email Verification

| Step | Actor | Action | System |
|------|-------|--------|--------|
| 1 | User | Submits `/register` form (name, email, password) | Validates input, checks reCAPTCHA v3 token |
| 2 | System | Hashes password, inserts `users` with status `inactive` | Generates 6-digit code, stores in Redis with TTL |
| 3 | System | Sends verification email via SMTP (`EmailLogService`) | Redirects to `/auth/verify/{userId}` |
| 4 | User | Enters 6-digit code on verify page | Looks up code in Redis, checks expiry and attempt limits |
| 5 | System | Code matches → activates account (`status_lookup_id` = active) | Logs user in, redirects to products page |
| 5b | System | Code wrong → increments attempt counter in Redis | After 5 failed attempts within 1 hour, blocks further tries |

> User can resend code (60s cooldown, max 5 per day). Failed attempts tracked in Redis.

---

### 2. Login

| Step | Actor | Action | System |
|------|-------|--------|--------|
| 1 | User | Submits `/login` form (email, password) | Checks reCAPTCHA, looks up user by email |
| 2 | System | Verifies password with `password_verify()` | Checks `deleted_at` is null and `status_lookup_id` = active |
| 3 | System | On success → `Auth` library sets session (`user_id`, `role`, `name`) | Updates `last_login_at`, redirects based on role |
| 4 | System | On failure → returns "Invalid email or password" | No distinction between wrong email vs wrong password |

---

### 3. Browsing & Cart

1. `User/Products` controller queries `products` where status = active and stock > 0
2. Cart is held in CI3 **session** (`$this->session->userdata('cart')`) — not persisted to DB
3. On checkout:
   - Unauthenticated → redirected to `/login`
   - Authenticated → proceeds to `/checkout`

---

### 4. Checkout & Payment

| Step | Actor | What happens |
|------|-------|--------------|
| 1 | User | Reviews cart at `/checkout`, clicks Place Order |
| 2 | System | `CheckoutService::checkout()` validates **idempotency key** from the form POST |
| 3 | System | If key previously processed → returns cached response (prevents double charge) |
| 4 | System | If new key → within a DB transaction: |
| 4a | | `OrderService::createOrder()` — creates `orders` row (status = pending, version = 1) + `order_items` rows with price snapshots |
| 4b | | `PaymentService::createPayment()` — creates `payments` row (payment_no = `PAY-...`, currency = USD) + `payment_attempts` row (attempt_no = 1, provider = stripe) |
| 4c | | `StripeService::createCheckoutSession()` — calls Stripe API with same idempotency key, line items, success/cancel URLs, metadata (order_id, payment_id) |
| 4d | | `PaymentService::saveStripeSession()` — saves `stripe_session_id` to the payment attempt |
| 5 | System | Commits transaction, caches response in `idempotency_keys` | Returns Stripe checkout URL to browser |
| 6 | User | Completes payment on Stripe-hosted page |
| 7 | Stripe | Redirects browser to `/payment/success?session_id=...` | (or `/payment/cancel`) |
| 8 | User/Payment | `success()` calls `PaymentService::fulfillPaymentBySession()` | Queries payment attempt by session ID, checks for duplicate processing |
| 8a | | In a DB transaction: updates attempt + payment status to `paid` | Creates `invoices` row (invoice_no = `INV-...`) |
| 8b | | Creates `receipts` row (receipt_no = `RCT-...`) linked to the invoice | Commits transaction |
| 9 | | Clears cart from session | Renders success page |

---

### 5. Webhook Processing

Stripe also sends an **async** webhook to `/webhooks/stripe`, independent of the browser redirect.

| Step | Actor | What happens |
|------|-------|--------------|
| 1 | Stripe | POSTs raw event payload + `Stripe-Signature` header to `/webhooks/stripe` |
| 2 | Controller | Reads raw body via `php://input` |
| 3 | `StripeService` | Verifies HMAC-SHA256 via `\Stripe\Webhook::constructEvent()` — fail → HTTP 400 |
| 4 | `PaymentService::webhookExists()` | Checks `stripe_webhook_events.event_id` (UNIQUE) — duplicate → HTTP 200, skip |
| 5 | `PaymentService::saveWebhookEvent()` | Inserts `stripe_webhook_events` row (processed = false) |
| 6 | Switch on `event->type`: |
| 6a | `checkout.session.completed` | `handleSuccessfulPayment()` — updates payment status to paid, creates `stripe_transactions` (with charge_id, payment_intent_id), creates `payment_events` entry, **decrements product stock** via `productrepository->decreaseStock()` |
| 6b | `payment_intent.payment_failed` | `handleFailedPayment()` — updates payment status to failed, creates `payment_events` entry |
| 7 | `markWebhookProcessed()` | Sets processed = true, processing_completed_at timestamp |
| 8 | | Returns HTTP 200 |

> **Invoice and receipt are NOT created in the webhook** — they are created in `User/Payment::success()` (the redirect handler). The webhook handles payment status, Stripe transaction recording, and stock decrement.

---

### 6. Viewing Documents

| Route | Controller | Access |
|-------|------------|--------|
| `/user/orders/history` | `User/Orders::history()` | User's own orders only |
| `/user/invoices/show/{id}` | `User/Invoices::show()` | Ownership check → 403 if not yours |
| `/user/receipts/show/{id}` | `User/Receipts::show()` | Ownership check → 403 if not yours |
| `/admin/invoices` | `Admin/Invoices` | Admin — all invoices |
| `/admin/receipts` | `Admin/Receipts` | Admin — all receipts |
| `/admin/stripe_transactions` | `Admin/StripeTransactions` | Admin — full payment/webhook trail |

---

### 7. Admin Management

- **Users** → `Admin/Users` — CRUD, assign roles, activate/deactivate
- **Roles** → `Admin/Roles` — CRUD role definitions
- **Permissions** → `Admin/Permissions` — CRUD permission codes
- **Role Permissions** → `Admin/Role_permissions` — assign permissions to roles
- **Products** → `Admin/Products` — CRUD with stock management, soft-delete
- **Lookups** → `Admin/Lookups` / `Admin/LookupGroups` — CRUD lookup values
- **Orders** → `Admin/Orders` — view all orders, filter by status
- **Invoices / Receipts** → read-only, paginated, filterable
- **Audit Log** → `Admin/AuditLogs` — browse all `audit_logs`

Auth enforced by `Auth_middleware` hook + role check (admin routes check `$this->auth->isAdmin()`).

---

### 8. API

Not yet implemented. The following controllers are stubs with no logic:

- `Api/V1/Auth.php` — planned: `POST /api/auth/login`
- `Api/V1/Invoices.php` — planned: `GET /api/invoices`
- `Api/V1/Receipts.php` — planned: `GET /api/receipts`

---

### 9. Logging Summary

| Table | Volume | Detail | Records |
|-------|--------|--------|---------|
| `audit_logs` | Low | High — old/new JSON data | Admin CRUD operations |
| `email_logs` | Medium | Status + response | Every sent email |
| `payment_events` | Medium | Event type + payload | Payment lifecycle events |
| `stripe_webhook_events` | Medium | Full Stripe payload | Every inbound webhook call |
