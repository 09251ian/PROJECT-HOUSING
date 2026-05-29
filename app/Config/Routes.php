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

$routes->get('/register', 'Auth::registerForm');
$routes->post('/register', 'Auth::register');

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

// Socket notification endpoints
$routes->post('/update-property-notify', 'SocketController::updatePropertyNotify');
$routes->post('/delete-property-notify', 'SocketController::deletePropertyNotify');
$routes->post('/archive-property-notify', 'SocketController::archivePropertyNotify');
$routes->post('/unarchive-property-notify', 'SocketController::unarchivePropertyNotify');
$routes->post('api/save-message', 'MessageController::saveMessage');
$routes->post('admin/delete-property', 'AdminController::deleteProperty');

// API Routes for real-time property fetching
$routes->get('/api/property/(:num)', 'SellerController::getProperty/$1');
$routes->get('/admin/get_counts', 'AdminController::getCounts');
$routes->post('/admin/update-offer-status', 'AdminController::updateOfferStatus');

$routes->get('admin/audit', 'AdminController::audit');
$routes->post('admin/archive-property', 'AdminController::archiveProperty');
$routes->post('admin/unarchive-property', 'AdminController::unarchiveProperty');
$routes->post('admin/update-offer-status', 'AdminController::updateOfferStatus');
$routes->post('make_offer', 'MakeOfferController::create');

$routes->get('/test-offer', function() {
    return 'Route test works!';
});

$routes->get('/test-offer-action', function() {
    return 'Route test works!';
});