# Technology Flow Guide — Payment Portal

**CodeIgniter 3 · PHP 7.3 · MySQL 8 · Stripe**

**Related:** [Technology Guide](technology-guide.md) · [Installation Guide](installation-guide.md) · [Database Design](database-design.md)

---

### 1. Registration & Login

| Step | Actor | Action | System |
|------|-------|--------|--------|
| 1 | User | Submits registration form | Validates, hashes password with `password_hash()`, inserts into `users` |
| 2 | System | — | Redirects to login with success message |
| 3 | User | Submits login form | `Auth_service` looks up user by email |
| 4 | System | Verifies with `password_verify()` | Sets session (`user_id`, `role`, `name`) or returns "invalid credentials" |
| 5 | System | Writes `activity_logs` entry | Redirects Admin → `/admin/dashboard`, User → `/account/orders` |

Failed logins are also logged to `activity_logs` (without password).

---

### 2. Browsing & Cart

1. Storefront queries `products` where status = active and stock > 0
2. Cart is held in CI3 session (`$this->session->userdata('cart')`) — **not** in the database
3. On checkout:
   - Unauthenticated users → `/auth/login` (cart preserved)
   - Authenticated users → order created
4. System creates one `orders` row (status `pending`) and one `order_items` row per cart line — `unit_price` is **snapshotted** so price changes won't affect history

---

### 3. Checkout & Payment

| Step | Actor | What happens |
|------|-------|--------------|
| 1 | User | Clicks Checkout |
| 2 | System | `Payment_service::createCheckoutSession()` builds a Stripe Session, stores `stripe_session_id` |
| 3 | System | 302-redirects user to Stripe-hosted checkout page |
| 4 | User | Completes (or abandons) payment on Stripe |
| 5 | Stripe | Redirects browser to `/checkout/success` or `/checkout/cancel` |
| 6 | Stripe | Sends `checkout.session.completed` to `/webhook/stripe` (async) |

The redirect (step 5) is **UX only** — it never marks an order paid. Only the webhook (step 6) can do that.

---

### 4. Webhook Processing

1. Stripe POSTs raw payload + `Stripe-Signature` header to `/webhook/stripe`
2. Controller reads raw body, recomputes HMAC-SHA256, compares with `hash_equals()` — mismatch → **400**
3. Checks `stripe_webhook_events` for `event_id` — exists → **200** (idempotency)
4. If new: inserts event (`processed = false`), then inside a DB transaction:
   - Order status → `paid`
   - Invoice row created
   - Receipt row created (against invoice)
   - `audit_logs` entry written
5. `processed` = true, returns **200**

For `payment_intent.payment_failed`: order marked `payment_failed`, no invoice/receipt.

---

### 5. Viewing Documents (Web)

| Route | Access |
|-------|--------|
| `/account/orders` | User's own orders only |
| `/account/invoices/{id}` | Ownership check → 403 if not yours |
| `/account/receipts/{id}` | Ownership check → 403 if not yours |
| `/admin/invoices` | Admin — all invoices |
| `/admin/receipts` | Admin — all receipts |
| `/admin/transactions` | Admin — full payment/webhook trail |

---

### 6. Admin Management

- **Create user:** Form → hash temp password → insert → `audit_logs` entry
- **Create/edit product:** Form → insert/update → `audit_logs` with `old_data`/`new_data`
- **View invoices/receipts/transactions:** Read-only, paginated, filterable
- **View audit log:** Browse all `audit_logs` and `activity_logs`

Admin access enforced by `Admin_auth` filter on every `/admin/*` route.

---

### 7. API Usage

1. Client calls `POST /api/auth/login` with email + password
2. Server verifies credentials, generates opaque token, stores **hash only** in `api_tokens`
3. Client sends `Authorization: Bearer <token>` on subsequent calls
4. `Api_auth` filter hashes token, looks it up, rejects expired/missing → **401**
5. Same ownership rules as web: non-admins see own records, admins see all

---

### 8. Logging Summary

| Table | Volume | Detail | What's recorded |
|-------|--------|--------|-----------------|
| `activity_logs` | High | Low | Logins, logouts, orders placed |
| `audit_logs` | Low | High (old/new data) | Admin CRUD, payment completions |
| `stripe_webhook_events` | Medium | Full payload | Every inbound Stripe event (idempotency) |
