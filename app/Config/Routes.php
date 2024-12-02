<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Api
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // api aktifitas
    $routes->get('aktifitas', 'ApiAktifitas::index');
    $routes->post('aktifitas/simpan', 'ApiAktifitas::create');
    $routes->get('aktifitas/edit/(:num)', 'ApiAktifitas::edit/$1');
    $routes->post('aktifitas/update/(:num)', 'ApiAktifitas::update/$1');
    $routes->delete('aktifitas/delete/(:num)', 'ApiAktifitas::delete/$1');

    // api kas
    $routes->get('kas', 'ApiKas::index');
    $routes->post('kas/simpan', 'ApiKas::create');
    $routes->get('kas/edit/(:num)', 'ApiKas::edit/$1');
    $routes->post('kas/update/(:num)', 'ApiKas::update/$1');
    $routes->delete('kas/delete/(:num)', 'ApiKas::delete/$1');

    // api fasilitas
    $routes->get('fasilitas', 'ApiFasilitas::index');
    $routes->post('fasilitas/simpan', 'ApiFasilitas::create');
    $routes->get('fasilitas/edit/(:num)', 'ApiFasilitas::edit/$1');
    $routes->post('fasilitas/update/(:num)', 'ApiFasilitas::update/$1');
    $routes->delete('fasilitas/delete/(:num)', 'ApiFasilitas::delete/$1');

    // api kegiatan
    $routes->get('kegiatan', 'ApiKegiatan::index');
    $routes->post('kegiatan/tambah', 'ApiKegiatan::new');
    $routes->post('kegiatan/simpan', 'ApiKegiatan::create');
    $routes->get('kegiatan/edit/(:num)', 'ApiKegiatan::edit/$1');
    $routes->post('kegiatan/update/(:num)', 'ApiKegiatan::update/$1');
    $routes->delete('kegiatan/delete/(:num)', 'ApiKegiatan::delete/$1');
    $routes->get('kegiatan/updateLPJ/(:num)', 'ApiKegiatan::UpdateLPJ/$1');

    // api pemberitahuan
    $routes->get('pemberitahuan', 'ApiPemberitahuan::index');
    $routes->post('pemberitahuan/simpan', 'ApiPemberitahuan::create');
    $routes->get('pemberitahuan/edit/(:num)', 'ApiPemberitahuan::edit/$1');
    $routes->post('pemberitahuan/update/(:num)', 'ApiPemberitahuan::update/$1');
    $routes->delete('pemberitahuan/delete/(:num)', 'ApiPemberitahuan::delete/$1');

    // api pengaduan
    $routes->get('pengaduan', 'ApiPengaduan::index');
    $routes->post('pengaduan/tambah', 'ApiPengaduan::new');
    $routes->post('pengaduan/simpan', 'ApiPengaduan::create');
    $routes->get('pengaduan/edit/(:num)', 'ApiPengaduan::edit/$1');
    $routes->post('pengaduan/update/(:num)', 'ApiPengaduan::update/$1');
    $routes->delete('pengaduan/delete/(:num)', 'ApiPengaduan::delete/$1');

    // api pengurus
    $routes->get('pengurus', 'ApiPengurus::index');
    $routes->post('pengurus/tambah', 'ApiPengurus::new');
    $routes->post('pengurus/simpan', 'ApiPengurus::create');
    $routes->get('pengurus/edit/(:num)', 'ApiPengurus::edit/$1');
    $routes->post('pengurus/update/(:num)', 'ApiPengurus::edit/$1');
    $routes->delete('pengurus/delete/(:num)', 'ApiPengurus::delete/$1');

    // api warga
    $routes->get('warga', 'ApiWarga::index');
    $routes->post('warga/simpan', 'ApiWarga::create');
    $routes->get('warga/edit/(:num)', 'ApiWarga::edit/$1');
    $routes->post('warga/update/(:num)', 'ApiWarga::update/$1');
    $routes->delete('warga/delete/(:num)', 'ApiWarga::delete/$1');

});
