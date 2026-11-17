<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->post('logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('transaksi', 'Transaksi::index');
    $routes->get('transaksi/baru', 'Transaksi::baru');
    $routes->post('transaksi/simpan', 'Transaksi::simpan');
    $routes->get('transaksi/(:num)', 'Transaksi::detail/$1');
    $routes->get('transaksi/(:num)/struk', 'Transaksi::struk/$1');
    $routes->post('transaksi/(:num)/status', 'Transaksi::status/$1');
});

$routes->group('', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('kategori', 'Kategori::index');
    $routes->get('kategori/tambah', 'Kategori::tambah');
    $routes->post('kategori/simpan', 'Kategori::simpan');
    $routes->get('kategori/(:num)/edit', 'Kategori::edit/$1');
    $routes->post('kategori/(:num)/update', 'Kategori::update/$1');
    $routes->post('kategori/(:num)/hapus', 'Kategori::hapus/$1');

    $routes->get('produk', 'Produk::index');
    $routes->get('produk/tambah', 'Produk::tambah');
    $routes->post('produk/simpan', 'Produk::simpan');
    $routes->get('produk/(:num)/edit', 'Produk::edit/$1');
    $routes->post('produk/(:num)/update', 'Produk::update/$1');
    $routes->post('produk/(:num)/hapus', 'Produk::hapus/$1');

    $routes->get('user', 'User::index');
    $routes->get('user/tambah', 'User::tambah');
    $routes->post('user/simpan', 'User::simpan');
    $routes->get('user/(:num)/edit', 'User::edit/$1');
    $routes->post('user/(:num)/update', 'User::update/$1');

    $routes->get('laporan', 'Laporan::index');
    $routes->get('laporan/csv', 'Laporan::csv');
    $routes->get('laporan/pdf', 'Laporan::pdf');
});
