-- Payment Portal — Seed Data
-- Run after schema.sql or migrations

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Clear previously seeded rows so the script is deterministic.
TRUNCATE TABLE `role_permissions`;
TRUNCATE TABLE `permissions`;
TRUNCATE TABLE `lookups`;
TRUNCATE TABLE `lookup_groups`;
TRUNCATE TABLE `order_items`;
TRUNCATE TABLE `payment_events`;
TRUNCATE TABLE `stripe_transactions`;
TRUNCATE TABLE `refunds`;
TRUNCATE TABLE `receipts`;
TRUNCATE TABLE `invoices`;
TRUNCATE TABLE `payment_attempts`;
TRUNCATE TABLE `payments`;
TRUNCATE TABLE `orders`;
TRUNCATE TABLE `products`;
TRUNCATE TABLE `stripe_webhook_events`;
TRUNCATE TABLE `idempotency_keys`;
TRUNCATE TABLE `api_tokens`;
TRUNCATE TABLE `audit_logs`;
TRUNCATE TABLE `activity_logs`;
TRUNCATE TABLE `email_logs`;
TRUNCATE TABLE `settings`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `roles`;

-- -----------------------------------------------------
-- Default roles and permissions
-- -----------------------------------------------------
INSERT INTO `roles` (`name`, `description`, `created_at`, `updated_at`)
VALUES
    ('admin', 'Administrator with full access to the portal', NOW(), NOW()),
    ('customer', 'Standard customer who can purchase products', NOW(), NOW());

INSERT INTO `permissions` (`code`, `name`, `description`, `created_at`, `updated_at`)
VALUES
    ('manage_users', 'Manage users', 'Create, update, and delete user accounts', NOW(), NOW()),
    ('manage_products', 'Manage products', 'Create, update, and delete products', NOW(), NOW()),
    ('manage_orders', 'Manage orders', 'View and update customer orders and order status', NOW(), NOW()),
    ('manage_payments', 'Manage payments', 'Process payments and refunds', NOW(), NOW()),
    ('view_reports', 'View reports', 'Access sales and activity reports', NOW(), NOW());

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `roles` r
JOIN `permissions` p ON p.code IN ('manage_users', 'manage_products', 'manage_orders', 'manage_payments', 'view_reports')
WHERE r.name = 'admin';

-- -----------------------------------------------------
-- Lookup groups and values
-- -----------------------------------------------------
INSERT INTO `lookup_groups` (`code`, `name`, `description`, `created_at`, `updated_at`)
VALUES
    ('user_status', 'User status', 'Active and inactive user states', NOW(), NOW()),
    ('product_status', 'Product status', 'Active and inactive product states', NOW(), NOW()),
    ('order_status', 'Order status', 'Order lifecycle states', NOW(), NOW()),
    ('payment_status', 'Payment status', 'Payment lifecycle states', NOW(), NOW()),
    ('refund_status', 'Refund status', 'Refund lifecycle states', NOW(), NOW()),
    ('product_category', 'Product category', 'Catalog product categories', NOW(), NOW());

INSERT INTO `lookups` (`group_id`, `code`, `value`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'user_status'), 'active', 'Active', 'User account is active', 1, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'user_status'), 'inactive', 'Inactive', 'User account is inactive', 2, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'product_status'), 'active', 'Active', 'Product is available for sale', 1, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'product_status'), 'inactive', 'Inactive', 'Product is not available', 2, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'order_status'), 'pending', 'Pending', 'Order has not yet been paid', 1, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'order_status'), 'paid', 'Paid', 'Order payment completed', 2, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'order_status'), 'failed', 'Failed', 'Order payment failed', 3, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'order_status'), 'cancelled', 'Cancelled', 'Order was cancelled', 4, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'order_status'), 'refunded', 'Refunded', 'Order has been refunded', 5, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status'), 'pending', 'Pending', 'Payment attempt pending', 1, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status'), 'paid', 'Paid', 'Payment has been completed', 2, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status'), 'failed', 'Failed', 'Payment has failed', 3, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status'), 'refunded', 'Refunded', 'Payment was refunded', 4, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status'), 'partially_refunded', 'Partially Refunded', 'Payment partially refunded', 5, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'refund_status'), 'pending', 'Pending', 'Refund is pending', 1, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'refund_status'), 'succeeded', 'Succeeded', 'Refund completed successfully', 2, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'refund_status'), 'failed', 'Failed', 'Refund failed', 3, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'refund_status'), 'cancelled', 'Cancelled', 'Refund was cancelled', 4, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'product_category'), 'software', 'Software', 'Software and SaaS products', 1, 1, NOW(), NOW()),
    ((SELECT id FROM `lookup_groups` WHERE `code` = 'product_category'), 'hardware', 'Hardware', 'Hardware and physical goods', 2, 1, NOW(), NOW());

-- -----------------------------------------------------
-- Default users
-- -----------------------------------------------------
INSERT INTO `users` (`role_id`, `name`, `email`, `password`, `status_lookup_id`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `roles` WHERE `name` = 'admin'), 'Platform Admin', 'admin@example.com', '$2y$10$cOt/EkR147f0mFI.e3XFIe2tuYKgvvXdJsu.X/I3KgRsVD7CqTbye', (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'user_status') AND `code` = 'active'), NOW(), NOW()),
    ((SELECT id FROM `roles` WHERE `name` = 'customer'), 'Example Customer', 'customer@example.com', '$2y$10$xFm0rEcUUOqkUeffGYeTxO/ma8EQUdBLFN1nXmp/RyThin0sq4svy', (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'user_status') AND `code` = 'active'), NOW(), NOW());

-- -----------------------------------------------------
-- Product catalog
-- -----------------------------------------------------
INSERT INTO `products` (`category_lookup_id`, `status_lookup_id`, `name`, `description`, `sku`, `price`, `created_by`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'product_category') AND `code` = 'software'), (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'product_status') AND `code` = 'active'), 'Starter plan', 'Monthly subscription for a single user.', 'SP-001', 49.99, (SELECT id FROM `users` WHERE `email` = 'admin@example.com'), NOW(), NOW()),
    ((SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'product_category') AND `code` = 'hardware'), (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'product_status') AND `code` = 'active'), 'Payment reader', 'Portable payment device for in-person payments.', 'HW-001', 129.99, (SELECT id FROM `users` WHERE `email` = 'admin@example.com'), NOW(), NOW());

-- -----------------------------------------------------
-- Orders and order items
-- -----------------------------------------------------
INSERT INTO `orders` (`user_id`, `order_no`, `status_lookup_id`, `total_amount`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `users` WHERE `email` = 'customer@example.com'), 'ORD-20260709-0001', (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'order_status') AND `code` = 'paid'), 179.98, NOW(), NOW());

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`)
VALUES
    ((SELECT id FROM `orders` WHERE `order_no` = 'ORD-20260709-0001'), (SELECT id FROM `products` WHERE `sku` = 'SP-001'), 1, 49.99, 49.99, NOW()),
    ((SELECT id FROM `orders` WHERE `order_no` = 'ORD-20260709-0001'), (SELECT id FROM `products` WHERE `sku` = 'HW-001'), 1, 129.99, 129.99, NOW());

-- -----------------------------------------------------
-- Payments and payment attempts
-- -----------------------------------------------------
INSERT INTO `payments` (`order_id`, `payment_no`, `amount`, `currency`, `payment_method`, `status_lookup_id`, `paid_at`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `orders` WHERE `order_no` = 'ORD-20260709-0001'), 'PAY-20260709-0001', 179.98, 'USD', 'stripe', (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status') AND `code` = 'paid'), NOW(), NOW(), NOW());

INSERT INTO `payment_attempts` (`payment_id`, `attempt_no`, `provider`, `stripe_session_id`, `payment_intent_id`, `amount`, `status_lookup_id`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `payments` WHERE `payment_no` = 'PAY-20260709-0001'), 1, 'stripe', 'cs_test_001', 'pi_test_001', 179.98, (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'payment_status') AND `code` = 'paid'), NOW(), NOW());

-- -----------------------------------------------------
-- Stripe event and transaction log
-- -----------------------------------------------------
INSERT INTO `stripe_webhook_events` (`event_id`, `event_type`, `processed`, `payload`, `created_at`, `processed_at`)
VALUES
    ('evt_test_001', 'payment_intent.succeeded', 1, '{"id":"pi_test_001","status":"succeeded"}', NOW(), NOW());

INSERT INTO `stripe_transactions` (`payment_id`, `payment_attempt_id`, `webhook_event_id`, `stripe_session_id`, `payment_intent_id`, `charge_id`, `currency`, `amount`, `provider_status`, `raw_payload`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `payments` WHERE `payment_no` = 'PAY-20260709-0001'), (SELECT id FROM `payment_attempts` WHERE `payment_id` = (SELECT id FROM `payments` WHERE `payment_no` = 'PAY-20260709-0001') AND `attempt_no` = 1), (SELECT id FROM `stripe_webhook_events` WHERE `event_id` = 'evt_test_001'), 'cs_test_001', 'pi_test_001', 'ch_test_001', 'USD', 179.98, 'succeeded', '{"status":"succeeded"}', NOW(), NOW());

-- -----------------------------------------------------
-- Payment events
-- -----------------------------------------------------
INSERT INTO `payment_events` (`payment_id`, `event_type`, `event_source`, `payload`, `created_at`)
VALUES
    ((SELECT id FROM `payments` WHERE `payment_no` = 'PAY-20260709-0001'), 'payment_completed', 'system', '{"order":"ORD-20260709-0001"}', NOW());

-- -----------------------------------------------------
-- Idempotency keys
-- -----------------------------------------------------
INSERT INTO `idempotency_keys` (`user_id`, `idempotency_key`, `request_hash`, `response_data`, `expires_at`, `created_at`)
VALUES
    ((SELECT id FROM `users` WHERE `email` = 'customer@example.com'), 'idem_20260709_0001', 'hash_0001', '{"status":"cached"}', DATE_ADD(NOW(), INTERVAL 1 DAY), NOW());

-- -----------------------------------------------------
-- Refunds
-- -----------------------------------------------------
INSERT INTO `refunds` (`payment_id`, `refund_no`, `stripe_refund_id`, `amount`, `reason`, `status_lookup_id`, `refunded_at`, `created_by`, `approved_by`, `approved_at`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `payments` WHERE `payment_no` = 'PAY-20260709-0001'), 'REF-20260709-0001', 're_test_001', 19.99, 'Partial refund for returned item', (SELECT id FROM `lookups` WHERE `group_id` = (SELECT id FROM `lookup_groups` WHERE `code` = 'refund_status') AND `code` = 'succeeded'), NULL, (SELECT id FROM `users` WHERE `email` = 'admin@example.com'), (SELECT id FROM `users` WHERE `email` = 'admin@example.com'), NOW(), NOW(), NOW());

-- -----------------------------------------------------
-- Invoices and receipts
-- -----------------------------------------------------
INSERT INTO `invoices` (`order_id`, `invoice_no`, `amount`, `status_lookup_id`, `issued_at`, `issued_by`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `orders` WHERE `order_no` = 'ORD-20260709-0001'), 'INV-20260709-0001', 179.98, NULL, NOW(), (SELECT id FROM `users` WHERE `email` = 'admin@example.com'), NOW(), NOW());

INSERT INTO `receipts` (`invoice_id`, `receipt_no`, `amount`, `status_lookup_id`, `issued_at`, `issued_by`, `created_at`, `updated_at`)
VALUES
    ((SELECT id FROM `invoices` WHERE `invoice_no` = 'INV-20260709-0001'), 'RCT-20260709-0001', 179.98, NULL, NOW(), (SELECT id FROM `users` WHERE `email` = 'admin@example.com'), NOW(), NOW());

-- -----------------------------------------------------
-- API tokens, audit logs, activity logs, and settings
-- -----------------------------------------------------
INSERT INTO `api_tokens` (`user_id`, `token_hash`, `expires_at`, `last_used_at`, `created_at`)
VALUES
    ((SELECT id FROM `users` WHERE `email` = 'customer@example.com'), '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef', DATE_ADD(NOW(), INTERVAL 30 DAY), NULL, NOW());

INSERT INTO `audit_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `created_at`)
VALUES
    ((SELECT id FROM `users` WHERE `email` = 'admin@example.com'), 'user.login', 'user', (SELECT id FROM `users` WHERE `email` = 'customer@example.com'), '127.0.0.1', 'Seed script entry', NOW());

INSERT INTO `activity_logs` (`user_id`, `activity_type`, `description`, `ip_address`, `created_at`)
VALUES
    ((SELECT id FROM `users` WHERE `email` = 'customer@example.com'), 'checkout', 'Customer completed checkout for order ORD-20260709-0001', '127.0.0.1', NOW());

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`, `created_at`, `updated_at`)
VALUES
    ('site_name', 'Payment Portal', 'Public portal name', NOW(), NOW()),
    ('support_email', 'support@example.com', 'Support email address', NOW(), NOW()),
    ('default_currency', 'USD', 'Default display currency', NOW(), NOW()),
    ('tax_rate', '0.00', 'Default sales tax rate', NOW(), NOW());

INSERT INTO `email_logs` (`user_id`, `email_to`, `subject`, `status_lookup_id`, `response`, `sent_at`)
VALUES
    ((SELECT id FROM `users` WHERE `email` = 'admin@example.com'), 'customer@example.com', 'Welcome to Payment Portal', NULL, 'Message queued', NOW());

SET FOREIGN_KEY_CHECKS = 1;
