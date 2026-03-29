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
$routes->get('/test', 'Auth::test');


$routes->get('/register', 'Auth::registerForm'); // for later
$routes->post('/register', 'Auth::register');    // for later

// WebSocket Test Routes
$routes->get('websocket-test', 'WebSocketTest::index');
$routes->get('websocket-test/status', 'WebSocketTest::status');

$routes->get('buyer/api/websocket-status', 'BuyerController::apiWebSocketStatus');
$routes->get('buyer/api/websocket-config', 'BuyerController::apiWebSocketConfig');
$routes->post('buyer/api/websocket-notify', 'BuyerController::apiSendWebSocketNotification');

// Buyer API Routes
$routes->get('buyer/api/properties', 'BuyerController::apiGetProperties');
$routes->post('buyer/api/offer', 'BuyerController::apiMakeOffer');
$routes->get('buyer/api/property/(:num)', 'BuyerController::apiGetProperty/$1');
$routes->get('buyer/api/offers', 'BuyerController::apiGetMyOffers');
$routes->post('buyer/api/offer/cancel', 'BuyerController::apiCancelOffer');

// Seller API Routes for WebSocket
$routes->post('seller/api/update-offer', 'SellerController::apiUpdateOffer');
// In Routes.php
$routes->post('seller/offerAction', 'SellerController::offerAction');
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
$routes->post('/seller/upload_image', 'SellerController::upload_image');

$routes->get('message/(:num)/(:num)', 'MessageController::chat/$1/$2');      // Display chat
$routes->post('message/ajax/(:num)/(:num)', 'MessageController::ajax/$1/$2'); // AJAX send
$routes->get('message/ajax/(:num)/(:num)', 'MessageController::ajax/$1/$2');  // AJAX fetch