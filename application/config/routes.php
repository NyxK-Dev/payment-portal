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
    ='Admin/Products/show/$1';

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
