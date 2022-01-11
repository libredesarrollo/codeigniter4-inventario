<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */


$routes->group('dashboard', ['namespace' => 'App\Controllers\Dashboard'], function ($routes) {
    $routes->resource('category');
    $routes->resource('tag');
    
    $routes->get('product/trace/(:num)', 'Product::trace/$1', ['as' => 'product.trace']);
    $routes->resource('product');

    $routes->get('demo-pdf', 'Product::demoPDF'); // DEMO

    $routes->post('product/add-stock/(:num)/(:num)', 'Product::addStock/$1/$2');
    $routes->post('product/exit-stock/(:num)/(:num)', 'Product::exitStock/$1/$2');

    $routes->get('user/get-by-type/(:alpha)', 'User::getUsers/$1/');
});




if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}