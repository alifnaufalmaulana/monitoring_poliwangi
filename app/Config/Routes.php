<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->setAutoRoute(true);

$routes->get('/api/gedung', 'API\ApiController::getGedung');
$routes->get('/api/lantai/(:num)', 'API\ApiController::getLantaiByGedung/$1');
$routes->get('api/perangkat/lantai/(:num)', 'API\ApiController::getPerangkatByLantai/$1');


$routes->get('home', 'Home::index');
$routes->get('perangkat', 'PerangkatController::index');
$routes->get('gedung', 'GedungController::index');

$routes->get('/laporan', 'LaporanController::index');
$routes->get('/laporan/create', 'LaporanController::create');
$routes->post('/laporan/store', 'LaporanController::store');
$routes->get('/laporan/edit/(:num)', 'LaporanController::edit/$1');
$routes->post('/laporan/update/(:num)', 'LaporanController::update/$1');
$routes->post('/laporan/delete/(:num)', 'LaporanController::delete/$1');

$routes->get('/perangkat', 'PerangkatController::index');
$routes->post('/perangkat/simpan', 'PerangkatController::simpan');
$routes->get('/perangkat/edit/(:num)', 'PerangkatController::edit/$1');
$routes->post('perangkat/update/(:num)', 'PerangkatController::update/$1');
$routes->delete('/perangkat/hapus/(:num)', 'PerangkatController::hapus/$1');
$routes->get('/perangkat/getLantai/(:num)', 'PerangkatController::getLantai/$1');
$routes->get('/perangkat/getRuangan/(:num)', 'PerangkatController::getRuangan/$1');
$routes->get('perangkat/getDenah/(:num)', 'PerangkatController::getDenah/$1');
$routes->get('perangkat/lantai/(:num)', 'PerangkatController::getPerangkatByLantai/$1');
$routes->get('perangkat/getPerangkat/(:num)', 'PerangkatController::getPerangkat/$1');

$routes->get('/riwayat-perangkat', 'RiwayatController::index');
