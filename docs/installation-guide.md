# Installation Guide — Payment Portal

**Related:** [Technology Guide](technology-guide.md) · [Technology Flow Guide](technology-flow-guide.md) · [Database Design](database-design.md)

---

### 1. Prerequisites

Docker 20+ / Compose v2 · Git · Composer 2.x · Stripe account (test mode) · ngrok

---

### 2. Clone

```
git clone <repo-url> payment-portal
cd payment-portal
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
| `RECAPTCHA_SITE_KEY` | Google reCAPTCHA admin console |
| `RECAPTCHA_SECRET` | Google reCAPTCHA admin console |
| `SMTP_HOST` / `SMTP_USER` / `SMTP_PASS` | Your SMTP provider |
| `SUPPORT_EMAIL` | From address for verification emails |
| `REDIS_HOST` / `REDIS_PORT` / `REDIS_PASSWORD` | Redis for verification code storage |

---

### 4. Build & Start

```
docker compose build
docker compose up -d
docker compose exec php composer install
```

Three containers start: **nginx** (port 8080), **php** (PHP 7.3 FPM), **mysql** (port 3310 external / 3306 internal).

---

### 5. Database

Run the CI3 migrations (creates all 23 tables):

```
docker compose exec php php index.php cli migrate
```

Then seed roles, permissions, and lookup data:

```
docker compose exec php php index.php cli seed
```

> Alternatively, import the raw SQL files: `docker compose exec mysql mysql -u root -p payment_portal < sql/schema.sql` and `docker compose exec mysql mysql -u root -p payment_portal < sql/seed.sql`. Password is `DB_ROOT_PASSWORD` from .env (default `root`).

---

### 6. Verify

- Storefront: `http://localhost:8080`
- Login: `http://localhost:8080/login`
- Admin dashboard: `http://localhost:8080/dashboard`

---

### 7. Stripe Webhooks

```
ngrok http 8080
```

Copy the `https://*.ngrok-free.app` URL. In Stripe Dashboard → Webhooks → Add endpoint:

- URL: `https://your-subdomain.ngrok-free.app/webhooks/stripe`
- Events: `checkout.session.completed` · `payment_intent.payment_failed`

Copy the signing secret (`whsec_...`) into `.env` as `STRIPE_WEBHOOK_SECRET`, then:

```
docker compose restart php
```

---

### 8. Test Full Purchase

1. Register at `/register` — a 6-digit verification code is sent via email
2. Enter the code on the verification page to activate your account
3. Sign in at `/login`
4. Browse products at `/products`, add to cart, go to `/checkout`
5. Complete payment on Stripe — card `4242 4242 4242 4242`, any future expiry, any CVC
6. Redirected to `/payment/success` — invoice and receipt generated
7. Check **My Orders** for downloads
8. Admin panel → verify invoices, receipts, and Stripe transactions

Test failure with card `4000 0000 0000 0002` — redirects to `/payment/cancel`, order stays unpaid.

---

### 9. API Testing

API controllers (`Api/V1/Auth`, `Api/V1/Invoices`, `Api/V1/Receipts`) are currently stubs — API authentication and endpoints are not yet implemented.

---

### 10. Common Issues

| Problem | Fix |
|---------|-----|
| HTTP 500 | Set `DB_HOST=mysql` in .env |
| Webhooks silent | ngrok URL changed — update in Stripe Dashboard |
| Webhook 400 | Check `STRIPE_WEBHOOK_SECRET` matches Stripe |
| Composer fails | Add `pdo_mysql`, `mbstring`, `curl`, `gd` to Dockerfile |
| DB connection refused | Check `DB_HOST=mysql` (container hostname), not `localhost` |

---

### 11. Tear Down

```
docker compose down      # stop, keep data
docker compose down -v   # stop, delete all data
```

---

### 12. Next Steps

- [Technology Guide](technology-guide.md) — architecture, security, API, schema
- [Technology Flow Guide](technology-flow-guide.md) — end-to-end workflows
- [Database Design](database-design.md) — ER diagram, indexes, decisions
