<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->setAutoRoute(true);

$routes->get('home', 'Home::index');
$routes->get('perangkat', 'PerangkatController::index');
$routes->get('gedung', 'GedungController::index');
