<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default Routes
|--------------------------------------------------------------------------
|
| The application entry point is the authentication module.
| Users are redirected here when visiting the root URL.
|
*/
$route['default_controller'] = 'Home';

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
| Friendly authentication URLs.
|
*/
$route['login'] = 'Auth/Login';
$route['login/authenticate'] = 'Auth/Login/authenticate';
$route['logout'] = 'Auth/Login/logout';
$route['register'] = 'Auth/Register';
$route['register/store'] = 'Auth/Register/store';
$route['dashboard'] = 'admin/dashboard/index';
$route['admin/lookupgroups']
    = 'Admin/LookupGroups/index';
$route['admin/lookupgroups/create']
    = 'Admin/LookupGroups/create';
$route['admin/lookupgroups/store']
    = 'Admin/LookupGroups/store';
$route['admin/lookupgroups/edit/(:num)']
    = 'Admin/LookupGroups/edit/$1';
$route['admin/lookupgroups/update/(:num)']
    = 'Admin/LookupGroups/update/$1';
$route['admin/lookupgroups/delete/(:num)']
    = 'Admin/LookupGroups/delete/$1';
$route['admin/lookups']
    = 'Admin/Lookups/index';
$route['admin/lookups/(:num)']
    = 'Admin/Lookups/index/$1';
$route['admin/lookups/create/(:num)']
    = 'Admin/Lookups/create/$1';
$route['admin/lookups/store/(:num)']
    = 'Admin/Lookups/store/$1';
$route['admin/lookups/edit/(:num)']
    = 'Admin/Lookups/edit/$1';
$route['admin/lookups/update/(:num)']
    = 'Admin/Lookups/update/$1';
$route['admin/lookups/delete/(:num)']
    = 'Admin/Lookups/delete/$1';
/*
|--------------------------------------------------------------------------
| Products Routes
|--------------------------------------------------------------------------
*/


$route['admin/products'] = 'Admin/Products/index';

$route['admin/products/create'] = 'Admin/Products/create';

$route['admin/products/store'] = 'Admin/Products/store';

$route['admin/products/edit/(:num)']
    = 'Admin/Products/edit/$1';

$route['admin/products/update/(:num)']
    = 'Admin/Products/update/$1';

$route['admin/products/show/(:num)']
    = 'Admin/Products/show/$1';

$route['admin/products/delete/(:num)']
    = 'Admin/Products/destroy/$1';



$route['products'] = 'User/Products/index';

$route['products/(:num)']
    = 'User/Products/show/$1';
/*
|--------------------------------------------------------------------------
| Reserved Routes
|--------------------------------------------------------------------------
*/

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//roles
$route['admin/roles']
    =
    'admin/Roles/index';


$route['admin/roles/create']
    =
    'admin/Roles/create';


$route['admin/roles/store']
    =
    'admin/Roles/store';


$route['admin/roles/edit/(:num)']
    =
    'admin/Roles/edit/$1';


$route['admin/roles/update/(:num)']
    =
    'admin/Roles/update/$1';


$route['admin/roles/delete/(:num)']
    =
    'admin/Roles/delete/$1';

// Permissions

$route['admin/permissions']
    =
    'admin/Permissions/index';


$route['admin/permissions/create']
    =
    'admin/Permissions/create';


$route['admin/permissions/store']
    =
    'admin/Permissions/store';


$route['admin/permissions/edit/(:num)']
    =
    'admin/Permissions/edit/$1';


$route['admin/permissions/update/(:num)']
    =
    'admin/Permissions/update/$1';


$route['admin/permissions/delete/(:num)']
    =
    'admin/Permissions/delete/$1';

//User Payment
$route['webhooks/stripe'] = 'webhooks/stripe/index';

$route['checkout'] =
    'user/checkout/index';

$route['checkout/placeOrder'] =
    'user/checkout/placeOrder';

$route['payment/success'] = 'User/Payment/success';
$route['payment/cancel']  = 'User/Payment/cancel';

$route['payment/paypal/success'] = 'user/Paypal/success';
$route['payment/paypal/cancel']  = 'user/Paypal/cancel';
/*
|--------------------------------------------------------------------------
| Load API Routes
|--------------------------------------------------------------------------
*/

require_once APPPATH . 'config/routes_api.php';



/*
|--------------------------------------------------------------------------
| Reserved Routes
|--------------------------------------------------------------------------
*/

$route['404_override'] = '';

$route['translate_uri_dashes'] = FALSE;
