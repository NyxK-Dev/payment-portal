# Technology Guide — Payment Portal

**CodeIgniter 3 · PHP 7.3 · MySQL 8 · Stripe**

**Related:** [Flow Guide](technology-flow-guide.md) · [Installation Guide](installation-guide.md) · [Database Design](database-design.md)

---

### 1. Purpose

Three actors:

| Actor | Can do |
|-------|--------|
| **Admin** | Manage users/roles/products/permissions/lookups, view all invoices/receipts/transactions/audit log |
| **User** | Browse & purchase, view own orders/invoices/receipts |
| **API Client** | Planned — controllers are stubs, not yet implemented |

Payments use Stripe Checkout + Webhooks. The browser redirect is UX only — only a signature-verified webhook updates payment state. Invoice and receipt generation happen on the success redirect handler, which queries Stripe to confirm payment.

---

### 2. Stack

| Layer | Choice | Reason |
|-------|--------|--------|
| Language | PHP 7.3 + CodeIgniter 3 | Mandated by brief |
| Database | MySQL 8 | Mandated |
| Front-end | AdminLTE 3 + Bootstrap 5 + jQuery | Responsive admin panel |
| Payments | Stripe Checkout + Webhooks | PCI scope stays with Stripe |
| Containers | Docker Compose (Nginx + PHP-FPM + MySQL) | Reproducible PHP 7.3 env |
| Webhook testing | ngrok | Tunnel localhost to Stripe |
| Email | CI3 Email library (SMTP) | Verification codes, transactional |
| reCAPTCHA | Google reCAPTCHA v3 | Bot protection on login/register |
| Cache / Queue | Redis (Predis client) | Verification code storage, rate limiting |
| PDF | dompdf via `PdfDocument` library | Invoice/receipt PDF generation |

---

### 3. Architecture

```
Browser → Nginx → PHP-FPM (Controllers → Services → Repositories) → MySQL
                                                      ↑
Stripe ──webhook──→ /webhooks/stripe
```

**Custom infrastructure:**
- `MY_Loader` — adds `service()`, `repository()`, `request()` load methods
- `MY_Controller` — base class with `render()`, auth helpers, role checking
- `Auth_middleware` hook — intercepts protected routes, checks session/auth
- `Auth` library — manages login state, session data, role checks
- `BaseService` — abstract class with audit logging helper
- `ValidationFactory` + `RuleBuilder` — structured form validation
- `RequestValidator` — reusable request input validation
- `PdfDocument` — dompdf wrapper for invoice/receipt PDFs
- `Seeder` library — CLI seed command for lookup/role/permission data

**Key routing:**
- `GET /login` → `Auth/Login::index()`
- `POST /login/authenticate` → `Auth/Login::authenticate()`
- `GET /register` → `Auth/Register::index()`
- `POST /register/store` → `Auth/Register::store()`
- `GET /checkout` → `User/Checkout::index()`
- `POST /checkout/placeOrder` → `User/Checkout::placeOrder()`
- `GET /payment/success` → `User/Payment::success()` (invoice/receipt created here)
- `GET /payment/cancel` → `User/Payment::cancel()`
- `POST /webhooks/stripe` → `Webhooks/Stripe::index()`

---

### 4. Services

| Service | Responsibility |
|---------|---------------|
| `Auth_service` | Login/logout, password hashing/verification, status checks |
| `CheckoutService` | Orchestrates full checkout — idempotency, order, payment, Stripe session |
| `PaymentService` | Webhook handling, payment/attempt CRUD, stock decrement, invoice/receipt fulfillment |
| `StripeService` | Stripe API calls — Checkout Sessions, Payment Intents, Charges, Refunds, webhook verification |
| `OrderService` | Order CRUD, order history, order items with price snapshots |
| `InvoiceService` | Invoice CRUD (via repository) |
| `ReceiptService` | Receipt CRUD (via repository) |
| `IdempotencyService` | Idempotency key lifecycle — acquire, complete, fail |
| `Verification_service` | 6-digit code generation, Redis storage, code verification, resend with cooldown |
| `Recaptcha_service` | reCAPTCHA v3 token verification |
| `EmailLogService` | Send SMTP emails + log to `email_logs` table |
| `AuditLogService` | Audit log creation with before/after data |
| `Product_service` | Product catalog, stock management |
| `RoleService` / `PermissionService` / `RolePermissionService` | RBAC management |
| `LookupService` / `LookupGroupService` | Lookup table management |
| `UserService` | User CRUD |

---

### 5. Database

23 tables. Full details in [Database Design](database-design.md). Key groups:

| Group | Tables |
|-------|--------|
| **RBAC** | `roles`, `permissions`, `role_permissions` |
| **Core** | `users`, `products` |
| **Orders** | `orders`, `order_items` (with price snapshots) |
| **Payments** | `payments`, `payment_attempts`, `payment_events`, `stripe_transactions`, `stripe_webhook_events`, `idempotency_keys` |
| **Documents** | `invoices`, `receipts` |
| **Refunds** | `refunds` |
| **Lookups** | `lookup_groups`, `lookups` (replaces hard-coded enums) |
| **Security** | `api_tokens` |
| **Logging** | `audit_logs`, `activity_logs`, `email_logs` |
| **Config** | `settings` |

---

### 6. Security

| Concern | How |
|---------|-----|
| Passwords | bcrypt via `password_hash()` / `password_verify()` |
| CSRF | CI3 global CSRF protection on all form posts |
| XSS | `html_escape()` on all output |
| SQLi | Query Builder for runtime queries |
| AuthZ | `Auth_middleware` hook + role check on admin routes |
| Account status | Inactive accounts (unverified email) cannot log in |
| reCAPTCHA | v3 token verification on register and login |
| Webhooks | HMAC-SHA256 via Stripe SDK `constructEvent()` |
| Secrets | `.env` git-ignored, `.env.example` as template |

---

### 7. API

API controllers exist at `Api/V1/Auth.php`, `Api/V1/Invoices.php`, `Api/V1/Receipts.php` but are **empty stubs** — not yet implemented. Planned endpoints:

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/auth/login` | POST | Exchange credentials for bearer token |
| `/api/invoices` | GET | List invoices (admin: all) |
| `/api/receipts` | GET | List receipts (admin: all) |

---

### 8. Logging

| Log | Audience | Content |
|-----|----------|---------|
| CI3 app logs (`application/logs/`) | Developers | Errors, webhook receipts, debug |
| `audit_logs` table | Admins | Admin CRUD with before/after data |
| `email_logs` table | Admins | Sent email records with status |

---

### 9. Known Limitations

- API endpoints are not yet functional (controllers are stubs)
- No rate limiting beyond reCAPTCHA
- Single currency (USD)
- Stock decrement on webhook — no row-level locking or reservation

---

### 10. Future Improvements

- Implement API token auth and endpoints
- PDF styling improvements for invoices/receipts
- Queue-based webhook processing for higher volume
- Multi-currency support
