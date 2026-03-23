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
$routes->get('/seller/dashboard', 'SellerController::dashboard');
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



