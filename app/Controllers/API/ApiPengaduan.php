<?php

namespace App\Controllers\API;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiPengaduan extends ResourceController
{
    protected $format    = 'json';
    protected $PengaduanModel;
    protected $WargaModel;
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'status' => 200,
            'message' => 'Success',
            'data' => $this->PengaduanModel->relasiWarga()
        ];

        return $this->respond($data, 200);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        $data = [
            'status' => 200,
            'message' => 'Success',
            'data' => $this->WargaModel->findAll()
        ];

        return $this->respond($data, 200);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (!$this->validate([
            'nik'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIK warga harus diisi'
                ]
            ],
            'keluhan'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keluhan warga harus diisi'
                ]
            ],
            'foto'    => [
                'rules' => 'max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar (max 3MB)',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'format gambar harus jpg/jpeg/png'
                ]
            ],
            'tgl&waktu'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal dan waktu harus diisi'
                ]
            ]
        ])) {
            $response = [
                'status' => 400,
                'error' => true,
                'validation' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $nik = $this->WargaModel->find($this->request->getVar('nik'));
        if (empty($nik)) {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'NIK warga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }

        $foto = $this->request->getFile('foto');
        if ($foto->getError() == 4) {
            $namaFoto = 'default.jpg';
        } else {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/pengaduan/', $namaFoto);
        }

        $data = [
            'nik' => $this->request->getVar('nik'),
            'keluhan' => $this->request->getVar('keluhan'),
            'foto' => $namaFoto,
            'tgl&waktu' => $this->request->getVar('tgl&waktu')
        ];

        $this->PengaduanModel->insert($data);
        $response = [
            'status' => 201,
            'error' => false,
            'message' => 'Data berhasil ditambahkan'
        ];
        return $this->respondCreated($response, 201);
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        $data = $this->PengaduanModel->relasiWargaById($id);
        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Success',
                'data' => $data,
                'warga' => $this->WargaModel->findAll()
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'Data tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        if (!$this->validate([
            'nik'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIK warga harus diisi'
                ]
            ],
            'keluhan'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keluhan warga harus diisi'
                ]
            ],
            'foto'    => [
                'rules' => 'max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar (max 3MB)',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'format gambar harus jpg/jpeg/png'
                ]
            ],
            'tgl&waktu'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal dan waktu harus diisi'
                ]
            ]
        ])) {
            $response = [
                'status' => 400,
                'error' => true,
                'validation' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $nik = $this->WargaModel->find($this->request->getVar('nik'));
        if (empty($nik)) {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'NIK warga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }

        $pengaduan = $this->PengaduanModel->find($id);
        if ($pengaduan) {
            $foto = $this->request->getFile('foto');
            if ($foto->getError() == 4) {
                $$namaFoto = $pengaduan['foto'];
            } else {
                $namaFoto = $foto->getRandomName();
                $foto->move('uploads/pengaduan/', $namaFoto);
                if ($pengaduan['foto'] != 'default.jpg') {
                    unlink('uploads/pengaduan/' . $pengaduan['foto']);
                }
            }

            $data = [
                'nik' => $this->request->getVar('nik'),
                'keluhan' => $this->request->getVar('keluhan'),
                'foto' => $namaFoto,
                'tgl&waktu' => $this->request->getVar('tgl&waktu')
            ];

            $this->PengaduanModel->update($id, $data);
            $response = [
                'status' => 202,
                'error' => false,
                'message' => 'Data berhasil diupdate'
            ];
            return $this->respond($response, 202);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'Data tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $pengaduan = $this->PengaduanModel->find($id);
        if ($pengaduan) {
            if ($pengaduan['foto'] != 'default.jpg') {
                unlink('uploads/pengaduan/' . $pengaduan['foto']);
            }
            $this->PengaduanModel->delete($id);
            $response = [
                'status' => 203,
                'error' => false,
                'message' => 'Data berhasil dihapus'
            ];
            return $this->respondDeleted($response, 203);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'Data tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }
}
