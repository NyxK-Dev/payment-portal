<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/
$route['api/v1/test'] = 'Api/V1/Test/index';

/*
|--------------------------------------------------------------------------
| JWT Testing
|--------------------------------------------------------------------------
*/
$route['api/v1/jwt-test'] = 'Api/V1/JwtTest/index';

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
$route['api/v1/auth/register']    = 'Api/V1/AuthApi/register';
$route['api/v1/auth/login']       = 'Api/V1/AuthApi/login';
$route['api/v1/auth/verify-code'] = 'Api/V1/AuthApi/verifyCode';
$route['api/v1/auth/resend-code'] = 'Api/V1/AuthApi/resendCode';
$route['api/v1/auth/refresh']     = 'Api/V1/AuthApi/refresh';
$route['api/v1/auth/logout']      = 'Api/V1/AuthApi/logout';

/*
|--------------------------------------------------------------------------
| Products API
|--------------------------------------------------------------------------
*/
$route['api/v1/products']['GET']           = 'Api/V1/Products/index';
$route['api/v1/products']['POST']          = 'Api/V1/Products/store';
$route['api/v1/products/(:num)']['GET']    = 'Api/V1/Products/show/$1';
$route['api/v1/products/(:num)']['PUT']    = 'Api/V1/Products/update/$1';
$route['api/v1/products/(:num)']['PATCH']  = 'Api/V1/Products/update/$1';
$route['api/v1/products/(:num)']['DELETE'] = 'Api/V1/Products/delete/$1';

/*
|--------------------------------------------------------------------------
| Profile API
|--------------------------------------------------------------------------
*/
$route['api/v1/profile'] = 'Api/V1/ProfileApi/index';

/*
|--------------------------------------------------------------------------
| Orders API
|--------------------------------------------------------------------------
*/
$route['api/v1/orders']['GET']                 = 'Api/V1/Orders/index';
$route['api/v1/orders']['POST']                = 'Api/V1/Orders/store';
$route['api/v1/orders/(:num)']['GET']          = 'Api/V1/Orders/show/$1';
$route['api/v1/orders/(:num)/status']['PATCH'] = 'Api/V1/Orders/updateStatus/$1';

/*
|--------------------------------------------------------------------------
| Admin Orders API
|--------------------------------------------------------------------------
*/
$route['api/v1/admin/orders']['GET'] = 'Api/V1/Orders/adminIndex';
/*
|--------------------------------------------------------------------------
| Invoice API
|--------------------------------------------------------------------------
*/
$route['api/v1/invoices']['GET']                      = 'Api/V1/Invoices/index';
$route['api/v1/invoices/(:num)']['GET']               = 'Api/V1/Invoices/show/$1';
$route['api/v1/invoices/(:num)/download']['GET']      = 'Api/V1/Invoices/download/$1';

/*
|--------------------------------------------------------------------------
| Admin Invoice API
|--------------------------------------------------------------------------
*/
$route['api/v1/admin/invoices']['GET']                = 'Api/V1/Invoices/adminIndex';
$route['api/v1/admin/invoices/(:num)']['GET']         = 'Api/V1/Invoices/adminShow/$1';
$route['api/v1/admin/invoices/(:num)/download']['GET'] = 'Api/V1/Invoices/adminDownload/$1';
