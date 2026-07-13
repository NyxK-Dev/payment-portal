# Installation Guide — Payment Portal

**Related:** [Technology Guide](technology-guide.md) · [Flow Guide](technology-flow-guide.md) · [Database Design](database-design.md)

---

### 1. Prerequisites

Docker 20+ / Compose v2 · Git · Composer 2.x · Stripe account (test mode) · ngrok · Postman

---

### 2. Clone & Enter

```
git clone <repo-url> ecommerce-portal
cd ecommerce-portal/project
```

---

### 3. Environment Variables

```
cp .env.example .env
```

Key values to set:

| Variable | Source |
|----------|--------|
| `STRIPE_PUBLIC_KEY` | Stripe Dashboard → API keys (pk_test_...) |
| `STRIPE_SECRET_KEY` | Stripe Dashboard → API keys (sk_test_...) |
| `STRIPE_WEBHOOK_SECRET` | set in Step 7 |
| `API_BEARER_TOKEN` | `openssl rand -base64 32` (macOS) or PowerShell equivalent |

---

### 4. Build & Start

```
docker compose build
docker compose up -d
docker compose exec php-fpm composer install
```

Three containers start: **nginx** (port 8080), **php-fpm** (PHP 7.3), **mysql** (port 3306).

---

### 5. Database

```
docker compose exec mysql mysql -u root -p payment_portal < database/schema.sql
docker compose exec mysql mysql -u root -p payment_portal < database/seed.sql
```

Password: `DB_ROOT_PASSWORD` from .env (default `root`).

---

### 6. Verify

- Storefront: `http://localhost:8080`
- Admin: `http://localhost:8080/admin/login`

---

### 7. Stripe Webhooks

```
ngrok http 8080
```

Copy the `https://*.ngrok-free.app` URL. In Stripe Dashboard → Webhooks → Add endpoint:

- URL: `https://your-subdomain.ngrok-free.app/webhook/stripe`
- Events: `checkout.session.completed`

Copy the signing secret (`whsec_...`) into `.env` as `STRIPE_WEBHOOK_SECRET`, then:

```
docker compose restart php-fpm
```

---

### 8. Test Purchase

1. Register/sign in at the storefront
2. Add product to cart → Checkout
3. Pay with `4242 4242 4242 4242`, any future expiry, any CVC
4. Redirected to `/checkout/success` — order marked paid, invoice + receipt generated
5. Check **My Orders** for downloads
6. Admin panel → verify in All Invoices/Receipts and Stripe Transaction Log

Test failure with card `4000 0000 0000 0002` — redirects to `/checkout/cancel`, order stays unpaid.

---

### 9. API Testing

Import `postman/ecommerce-portal.postman_collection.json`. Set `base_url` to `http://localhost:8080` and `bearer_token` to your `API_BEARER_TOKEN`.

- `GET /api/invoices` — your records (all records if admin token)
- `GET /api/receipts` — your records (all records if admin token)

---

### 10. Common Issues

| Problem | Fix |
|---------|-----|
| HTTP 500 | Set `DB_HOST=mysql` in .env |
| Webhooks silent | ngrok URL changed — update in Stripe Dashboard |
| Webhook 400 | Check `STRIPE_WEBHOOK_SECRET` matches Stripe |
| Composer fails | Add `pdo_mysql`, `mbstring`, `curl`, `gd` to Dockerfile |

---

### 11. Tear Down

```
docker compose down      # stop, keep data
docker compose down -v   # stop, delete data
```

---

### 12. Next Steps

- [Technology Guide](technology-guide.md) — architecture, security, API, schema
- [Technology Flow Guide](technology-flow-guide.md) — end-to-end workflows
- [Database Design](database-design.md) — ER diagram, indexes, decisions
