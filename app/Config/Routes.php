<?php

use CodeIgniter\Router\RouteCollection;
use app\Models;
use app\Controllers\Auth;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::loginForm');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

$routes->get('/register', 'Auth::registerForm'); // for later
$routes->post('/register', 'Auth::register');    // for later

$routes->get('/buyer/dashboard', 'BuyerController::dashboard');
$routes->get('/buyer/favorites', 'FavoriteController::favorites');
$routes->post('/buyer/favorites/toggle', 'FavoriteController::toggleFavorite');
$routes->get('/seller/dashboard', 'SellerController::dashboard');

// Admin
$routes->get('/admin/dashboard', 'AdminController::dashboard');
$routes->get('/admin/users', 'AdminController::users');
$routes->get('/admin/properties', 'AdminController::properties');

$routes->match(['get','post'], '/admin/add_property', 'AdminController::addProperty');
$routes->match(['get','post'], '/admin/edit_property/(:num)', 'AdminController::editProperty/$1');

$routes->get('/admin/offers', 'AdminController::offers');
$routes->get('/admin/payments', 'AdminController::payments');
$routes->post('/seller/add_property', 'SellerController::addProperty');
$routes->post('/seller/offer_action', 'SellerController::offerAction');

$routes->get('/seller/archived', 'SellerController::archived');
$routes->match(['get','post'], '/seller/edit_property/(:num)', 'SellerController::editProperty/$1');
$routes->post('/seller/archive', 'SellerController::archive');
$routes->post('/seller/unarchive', 'SellerController::unarchive');

$routes->get('/profile/(:num)', 'ProfileController::view/$1');

$routes->post('/make_offer', 'MakeOfferController::create');
$routes->match(['get','post'], '/message/(:num)/(:num)', 'MessageController::chat/$1/$2');
$routes->post('/seller/delete', 'SellerController::delete');



