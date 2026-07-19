<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';


define(
    'BASEPATH',
    dirname(__DIR__) . '/system/'
);


define(
    'APPPATH',
    dirname(__DIR__) . '/application/'
);


function &get_instance()
{
    global $CI;

    return $CI;
}


/*
|--------------------------------------------------------------------------
| Interfaces
|--------------------------------------------------------------------------
*/

require_once APPPATH . 'interfaces/RoleRepositoryInterface.php';

require_once APPPATH . 'interfaces/PermissionRepositoryInterface.php';

require_once APPPATH . 'interfaces/AuditLogRepositoryInterface.php';

require_once APPPATH . 'interfaces/InvoiceRepositoryInterface.php';

require_once APPPATH . 'interfaces/ReceiptRepositoryInterface.php';

require_once APPPATH . 'interfaces/LookupRepositoryInterface.php';

require_once APPPATH . 'interfaces/UserRepositoryInterface.php';

require_once APPPATH . 'interfaces/IdempotencyInterface.php';

require_once APPPATH . 'interfaces/InvoiceRepositoryInterface.php';

require_once APPPATH . 'interfaces/OrderInterface.php';

require_once APPPATH . 'interfaces/OrderItemInterface.php';

require_once APPPATH . 'interfaces/PaymentInterface.php';

require_once APPPATH . 'interfaces/PaymentAttemptInterface.php';

require_once APPPATH . 'interfaces/StripeWebhookEventInterface.php';

require_once APPPATH . 'interfaces/StripeTransactionInterface.php';

require_once APPPATH . 'interfaces/PaymentEventInterface.php';

require_once APPPATH . 'interfaces/ProductInterface.php';

require_once APPPATH . 'interfaces/UserRepositoryInterface.php';

require_once APPPATH . 'interfaces/ReceiptRepositoryInterface.php';

require_once APPPATH . 'interfaces/LookupGroupRepositoryInterface.php';

require_once APPPATH . 'interfaces/LookupRepositoryInterface.php';

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/

require_once APPPATH . 'services/RoleService.php';

require_once APPPATH . 'services/PermissionService.php';

require_once APPPATH . 'services/AuditLogService.php';

require_once APPPATH . 'services/AccountingService.php';

require_once APPPATH . 'libraries/Auth.php';

require_once APPPATH . 'services/Auth_service.php';

require_once APPPATH . 'services/OrderService.php';

require_once APPPATH . 'services/PaymentService.php';

require_once APPPATH . 'services/StripeService.php';

require_once APPPATH . 'services/IdempotencyService.php';

require_once APPPATH . 'services/CheckoutService.php';

require_once APPPATH . 'services/InvoiceService.php';

require_once APPPATH . 'services/BaseService.php';

require_once APPPATH . 'services/Product_Service.php';

require_once APPPATH . 'services/UserService.php';

require_once APPPATH . 'services/ReceiptService.php';

require_once APPPATH . 'services/LookupGroupService.php';

require_once APPPATH . 'services/LookupService.php';
require_once APPPATH . 'services/Recaptcha_service.php';
require_once APPPATH . 'services/Verification_service.php';
require_once APPPATH . 'services/EmailLogService.php';
require_once APPPATH . 'services/Auth_Service.php';
require_once APPPATH . 'services/EmailService.php';
require_once APPPATH . 'services/RegisterService.php';
require_once APPPATH . 'repositories/LookupRepository.php';
require_once APPPATH . 'validators/RegisterValidator.php';
require_once APPPATH . 'services/PaymentGatewayResolver.php';
