<?php

require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

require_once APPPATH . 'repositories/RoleRepository.php';

require_once APPPATH . 'core/ServiceContainer.php';


$container = new ServiceContainer();


$container->bind( RoleRepositoryInterface::class, RoleRepository::class);

$container->bind(PermissionRepositoryInterface::class,PermissionRepository::class);

$container->bind(RolePermissionRepositoryInterface::class,RolePermissionRepository::class);

$container->bind(UserRepositoryInterface::class,UserRepository::class);

$container->bind(AuditLogRepositoryInterface::class,AuditLogRepository::class);

$container->bind(EmailLogRepositoryInterface::class,EmailLogRepository::class);

$container->bind(IdempotencyInterface::class,IdempotencyRepository::class);

$container->bind(InvoiceRepositoryInterface::class,InvoiceRepository::class);

$container->bind(LookupRepositoryInterface::class,LookupRepository::class);

$container->bind(LookupGroupRepositoryInterface::class,LookupGroupRepository::class);

$container->bind(OrderInterface::class,OrderRepository::class);

$container->bind(OrderItemInterface::class,OrderItemRepository::class);

$container->bind(PaymentAttemptInterface::class,PaymentAttemptRepository::class);

$container->bind(PaymentEventInterface::class,PaymentEventRepository::class);

$container->bind(PaymentInterface::class,PaymentRepository::class);

$container->bind(ProductInterface::class,ProductRepository::class);

$container->bind(ReceiptRepositoryInterface::class,ReceiptRepository::class);

$container->bind(StripeTransactionInterface::class,StripeTransactionRepository::class);

$container->bind(StripeWebhookEventInterface::class,StripeWebhookEventRepository::class);


$GLOBALS['container'] = $container;