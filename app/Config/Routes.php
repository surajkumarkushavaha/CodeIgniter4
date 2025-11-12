<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// Admin Routes
$routes->get('admin/login', 'AdminController::login');
$routes->post('admin/login', 'AdminController::login');
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/products', 'AdminController::products');
$routes->get('admin/orders', 'AdminController::orders');
$routes->get('admin/logout', 'AdminController::logout');

// API Routes
$routes->group('api', function($routes) {
    // Products API
    $routes->get('products', 'Api\Products::index');
    $routes->get('products/(:num)', 'Api\Products::show/$1');
    $routes->post('products', 'Api\Products::create');
    $routes->put('products/(:num)', 'Api\Products::update/$1');
    $routes->delete('products/(:num)', 'Api\Products::delete/$1');
    
    // Cart API
    $routes->get('cart', 'Api\Cart::index');
    $routes->post('cart', 'Api\Cart::create');
    $routes->put('cart/(:num)', 'Api\Cart::update/$1');
    $routes->delete('cart/(:num)', 'Api\Cart::delete/$1');
    
    // Checkout API
    $routes->post('checkout', 'Api\Checkout::create');
});

$routes->get('/', 'Home::index');


