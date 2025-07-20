<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Login & Logout
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::doLogin');
$routes->get('logout', 'AuthController::logout');


// API
$routes->group('api', ['namespace' => 'App\Controllers\API'], function ($routes) {
    $routes->get('gedung', 'ApiController::getGedung');
    $routes->get('lantai/(:num)', 'ApiController::getLantaiByGedung/$1');
    $routes->get('ruangan/(:num)', 'ApiController::getRuanganByLantai/$1');
    $routes->get('perangkat/(:num)', 'ApiController::getPerangkatDetails/$1');
    $routes->get('perangkat/lantai/(:num)', 'ApiController::getPerangkatByLantai/$1');
});


// Halaman Umum
$routes->get('home', 'Home::index');


// Akses riwayat perangkat untuk admin dan keamanan
$routes->group('', ['filter' => 'role:admin,keamanan'], function ($routes) {
    $routes->get('riwayat-perangkat', 'RiwayatController::index');
});


// ========== GROUP ADMIN ==========
$routes->group('', ['filter' => 'role:admin'], function ($routes) {
    // Menu Gedung
    $routes->get('gedung', 'GedungController::index');

    // Menu Perangkat
    $routes->get('perangkat', 'PerangkatController::index');
    $routes->post('perangkat/simpan', 'PerangkatController::simpan');
    $routes->get('perangkat/edit/(:num)', 'PerangkatController::edit/$1');
    $routes->post('perangkat/update/(:num)', 'PerangkatController::update/$1');
    $routes->delete('perangkat/hapus/(:num)', 'PerangkatController::hapus/$1');
    $routes->get('perangkat/getLantai/(:num)', 'PerangkatController::getLantai/$1');
    $routes->get('perangkat/getRuangan/(:num)', 'PerangkatController::getRuangan/$1');
    $routes->get('perangkat/getDenah/(:num)', 'PerangkatController::getDenah/$1');
    $routes->get('perangkat/lantai/(:num)', 'PerangkatController::getPerangkatByLantai/$1');
    $routes->get('perangkat/getPerangkat/(:num)', 'PerangkatController::getPerangkat/$1');
});


// ========== GROUP PIHAK KEAMANAN ==========
$routes->group('', ['filter' => 'role:keamanan'], function ($routes) {
    // Menu Laporan
    $routes->get('laporan', 'LaporanController::index');
    $routes->post('laporan/simpan', 'LaporanController::simpan');
    $routes->get('laporan/edit/(:num)', 'LaporanController::edit/$1');
    $routes->post('laporan/update/(:num)', 'LaporanController::update/$1');
    $routes->delete('laporan/hapus/(:num)', 'LaporanController::hapus/$1');
});
