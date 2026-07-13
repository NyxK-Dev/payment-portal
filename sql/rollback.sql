-- Payment Portal — Rollback (drops all application tables)
-- WARNING: This permanently deletes all data.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `email_logs`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `api_tokens`;
DROP TABLE IF EXISTS `receipts`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `refunds`;
DROP TABLE IF EXISTS `idempotency_keys`;
DROP TABLE IF EXISTS `payment_events`;
DROP TABLE IF EXISTS `stripe_transactions`;
DROP TABLE IF EXISTS `stripe_webhook_events`;
DROP TABLE IF EXISTS `payment_attempts`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `lookups`;
DROP TABLE IF EXISTS `lookup_groups`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `invoice_line_items`;
DROP TABLE IF EXISTS `migrations`;

SET FOREIGN_KEY_CHECKS = 1;
