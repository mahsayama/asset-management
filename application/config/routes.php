<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth Routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';

// Dashboard & Main Views
$route['dashboard'] = 'dashboard/index';
$route['inventory'] = 'inventory/index';
$route['reports'] = 'reports/index';
$route['settings'] = 'settings/index';

// Inventory Action Routes
$route['tambah'] = 'inventory/create';
$route['edit/(:num)'] = 'inventory/edit/$1';
$route['hapus/(:num)'] = 'inventory/destroy/$1';
$route['inventory/bulk_delete'] = 'inventory/bulk_destroy';
$route['asset/(:num)/detail'] = 'inventory/show/$1';
$route['inventory/import'] = 'inventory/import_excel';
$route['inventory/import/template'] = 'inventory/download_template';

// Settings & Admin User Actions
$route['settings/user'] = 'settings/store_user';
$route['settings/user/password'] = 'settings/update_password';
$route['settings/user/(:num)'] = 'settings/destroy_user/$1';
$route['settings/master'] = 'settings/store_master_data';
$route['settings/delete/(:any)/(:num)'] = 'settings/destroy_master_data/$1/$2';

// Report Exports
$route['reports/export/csv'] = 'reports/export_csv';
