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
    $routes->post('kegiatan/tambah', 'ApiKegiatan::new');
    $routes->post('kegiatan/simpan', 'ApiKegiatan::create');
    $routes->get('kegiatan/edit/(:num)', 'ApiKegiatan::edit/$1');
    $routes->post('kegiatan/update/(:num)', 'ApiKegiatan::update/$1');
    $routes->delete('kegiatan/delete/(:num)', 'ApiKegiatan::delete/$1');
    $routes->get('kegiatan/updateLPJ/(:num)', 'ApiKegiatan::UpdateLPJ/$1');

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
    $routes->get('pengurus/edit/(:num)', 'ApiPengurus::edit/$1');
    $routes->post('pengurus/update/(:num)', 'ApiPengurus::edit/$1');
    $routes->delete('pengurus/delete/(:num)', 'ApiPengurus::delete/$1');

    // api warga
    $routes->get('warga', 'ApiWarga::index');
    $routes->post('warga/simpan', 'ApiWarga::create');
    $routes->get('warga/edit/(:num)', 'ApiWarga::edit/$1');
    $routes->post('warga/update/(:num)', 'ApiWarga::update/$1');
    $routes->delete('warga/delete/(:num)', 'ApiWarga::delete/$1');
    
    // check login
    $routes->get('login', 'ApiLogin::login');
    $routes->get('login/pengurus', 'ApiLogin::loginPengurus');
});
