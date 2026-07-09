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
| Reserved Routes
|--------------------------------------------------------------------------
*/
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
