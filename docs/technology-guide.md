# Technology Guide — Payment Portal

**CodeIgniter 3 · PHP 7.3 · MySQL 8 · Stripe**

**Related:** [Flow Guide](technology-flow-guide.md) · [Installation Guide](installation-guide.md) · [Database Design](database-design.md)

---

### 1. Purpose

Three actors:

| Actor | Can do |
|-------|--------|
| **Admin** | Manage users/products, view all invoices/receipts/transactions/audit log |
| **User** | Browse & purchase, view own invoices/receipts |
| **API Client** | Authenticate via token, retrieve invoices/receipts |

Payments use Stripe Checkout + Webhooks — **never** trust the browser redirect for payment confirmation.

---

### 2. Stack

| Layer | Choice | Reason |
|-------|--------|--------|
| Language | PHP 7.3 + CodeIgniter 3 | Mandated by brief |
| Database | MySQL 8 | Mandated — window functions, CHECK constraints |
| Front-end | Bootstrap 5 + AdminLTE 3 | Responsive, open-source |
| Payments | Stripe Checkout + Webhooks | PCI scope stays with Stripe |
| Containers | Docker Compose (Nginx + PHP-FPM + MySQL) | Reproducible PHP 7.3 env |

---

### 3. Architecture

```
Browser → Nginx → PHP-FPM (Controllers → Services → Repositories) → MySQL
                                                      ↑
Stripe ──webhook──→ /webhook/stripe
```

| Layer | Does |
|-------|------|
| **Controllers** | Validate input, call service, return view/JSON. No business logic. |
| **Services** | `Payment_service`, `Invoice_service`, `Receipt_service`, `Auth_service` |
| **Repositories** | Wrap CI3 Models — one per aggregate |

Key rule: the browser redirect after payment is **UX only**. Only a signature-verified webhook can mark an order paid.

---

### 4. Database

18 tables. Full details in [Database Design](database-design.md).

| Table | Purpose |
|-------|---------|
| `roles`, `permissions`, `role_permissions` | RBAC — data-driven auth |
| `users` | Accounts, soft-deletable |
| `products` | Catalog, soft-deletable |
| `orders`, `order_items` | Purchases with price snapshots |
| `invoices`, `receipts` | Financial documents (immutable) |
| `stripe_transactions`, `stripe_webhook_events` | Stripe events + idempotency |
| `api_tokens` | Hashed bearer tokens |
| `audit_logs`, `activity_logs` | Compliance + activity trails |
| `lookup_groups`, `lookups` | Replace hard-coded enums |
| `settings`, `email_logs` | Config + email records |

**Key patterns:**
- Lookup tables replace hard-coded status enums (data change, not migration)
- Webhook idempotency via `stripe_webhook_events.event_id` UNIQUE
- Soft deletes preserve historical orders
- Price snapshots at `order_items.unit_price` keep invoices accurate

---

### 5. Security

| Concern | How |
|---------|-----|
| Passwords | bcrypt via `password_hash()` / `password_verify()` |
| CSRF | CI3 global CSRF protection |
| XSS | `html_escape()` on all output |
| SQLi | Query Builder only — no raw SQL |
| AuthZ | Role + ownership check on every controller method |
| Webhooks | HMAC-SHA256 signature verified on raw body |
| Secrets | `.env` git-ignored, `.env.example` as template |

---

### 6. API

All responses use this envelope:

```json
{ "success": true, "data": {}, "message": "OK", "errors": null }
```

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `/api/auth/login` | POST | — | Get bearer token |
| `/api/invoices` | GET | Bearer | List invoices (admin: all) |
| `/api/invoices/{id}` | GET | Bearer | Single invoice |
| `/api/receipts` | GET | Bearer | List receipts (admin: all) |
| `/api/receipts/{id}` | GET | Bearer | Single receipt |
| `/webhook/stripe` | POST | Stripe sig | Payment confirmation |

Tokens are opaque, hashed, expiring — stored in `api_tokens`.

---

### 7. Logging

| Log | Audience | Content |
|-----|----------|---------|
| Application log (`log_message()`) | Developers | Errors, webhooks, auth failures — rotated on disk |
| `audit_logs` table | Admins | Admin CRUD, payments — structured DB |

---

### 8. Known Limitations

- Email delivery — hook exists, not implemented
- Stock locking — transaction + row lock, not full reservation
- Single currency only

---

### 9. Future Improvements

- Interface-based repositories
- Rate limiting on `/api/auth/login`
- PDF generation via dompdf
- Queue-based webhook processing
