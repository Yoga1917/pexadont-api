<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Api
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // api rkb
    $routes->get('rkb', 'ApiRkb::index');
    $routes->post('rkb/simpan', 'ApiRkb::create');

    // api kas
    $routes->get('kas', 'ApiKas::index');
    $routes->get('kas/last', 'ApiKas::lastData');
    $routes->get('kas/publish', 'ApiKas::publishData');
    $routes->post('kas/publish/simpan', 'ApiKas::publish');
    $routes->get('kas/pemasukan', 'ApiKas::pemasukanData');
    $routes->get('kas/pengeluaran', 'ApiKas::pengeluaranData');
    $routes->post('kas/pemasukan/simpan', 'ApiKas::pemasukan');
    $routes->post('kas/pengeluaran/simpan', 'ApiKas::pengeluaran');

    // api fasilitas
    $routes->get('fasilitas', 'ApiFasilitas::index');
    $routes->post('fasilitas/simpan', 'ApiFasilitas::create');
    $routes->get('fasilitas/edit/(:num)', 'ApiFasilitas::edit/$1');
    $routes->post('fasilitas/update/(:num)', 'ApiFasilitas::update/$1');

    // api kegiatan
    $routes->get('kegiatan', 'ApiKegiatan::index');
    $routes->post('kegiatan/simpan', 'ApiKegiatan::create');
    $routes->post('kegiatan/lpj', 'ApiKegiatan::lpj');

    // api pemberitahuan
    $routes->get('pemberitahuan', 'ApiPemberitahuan::index');
    $routes->post('pemberitahuan/simpan', 'ApiPemberitahuan::create');

    // api pengaduan
    $routes->get('pengaduan', 'ApiPengaduan::index');
    $routes->post('pengaduan/simpan', 'ApiPengaduan::create');
    $routes->get('pengaduan/jenis/(:any)', 'ApiPengaduan::jenis/$1');
    $routes->get('pengaduan/warga/(:any)', 'ApiPengaduan::warga/$1');
    $routes->post('pengaduan/balas', 'ApiPengaduan::balas');

    // api pengurus
    $routes->get('pengurus', 'ApiPengurus::index');
    $routes->post('pengurus/simpan', 'ApiPengurus::create');
    $routes->get('pengurus/show/(:num)', 'ApiPengurus::show/$1');

    // api warga
    $routes->get('warga', 'ApiWarga::index');
    $routes->post('warga/simpan', 'ApiWarga::create');
    $routes->get('warga/edit/(:num)', 'ApiWarga::edit/$1');
    $routes->post('warga/update/(:num)', 'ApiWarga::update/$1');
    $routes->get('warga/terima', 'ApiWarga::terima');
    $routes->get('warga/tolak', 'ApiWarga::tolak');
    
    // check login
    $routes->get('login', 'ApiLogin::login');
    $routes->get('login/pengurus', 'ApiLogin::loginPengurus');
    
    // password modify
    $routes->get('password/reset', 'ApiLogin::passwordReset');
    $routes->post('password/ubah', 'ApiLogin::passwordUbah');
});
