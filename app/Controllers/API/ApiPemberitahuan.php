<?php

namespace App\Controllers\API;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiPemberitahuan extends ResourceController
{
    protected $modelName = 'App\Models\PemberitahuanModel';
    protected $format    = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'status'    => 200,
            'mesage'    => 'Success',
            'data'      => $this->model->findAll()
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
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (!$this->validate([
            'pemberitahuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Pemberitahuan harus diisi.'
                ]
            ],
            'deskripsi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi harus diisi.'
                ]
            ],
            'tgl' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal harus diisi.'
                ]
            ],
            'foto'  => [
                'rules' => 'max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran foto terlalu besar. Maksimal 3MB.',
                    'is_image' => 'File yang diupload bukan foto.',
                    'mime_in'  => 'Format foto harus jpg/jpeg/png.'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'error'     => true,
                'message'   => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $foto = $this->request->getFile('foto');
        if ($foto->getError() == 4) {
            $namaFoto = 'default.jpg';
        } else {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/pemberitahuan/', $namaFoto);
        }

        $data = [
            'pemberitahuan'  => $this->request->getPost('pemberitahuan'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'tgl'            => $this->request->getPost('tgl'),
            'foto'           => $namaFoto
        ];

        $this->model->insert($data);
        $response = [
            'status'    => 201,
            'error'     => false,
            'message'   => 'Data berhasil disimpan.'
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
        $data = $this->model->find($id);
        if ($data) {
            $response = [
                'status'    => 200,
                'message'   => 'Success',
                'data'      => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data tidak ditemukan.'
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
            'pemberitahuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Pemberitahuan harus diisi.'
                ]
            ],
            'deskripsi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi harus diisi.'
                ]
            ],
            'tgl' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal harus diisi.'
                ]
            ],
            'foto'  => [
                'rules' => 'max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran foto terlalu besar. Maksimal 3MB.',
                    'is_image' => 'File yang diupload bukan foto.',
                    'mime_in'  => 'Format foto harus jpg/jpeg/png.'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'error'     => true,
                'message'   => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $data = $this->model->find($id);
        if ($data) {
            $foto = $this->request->getFile('foto');
            if ($foto->getError() == 4) {
                $namaFoto = $data['foto'];
            } else {
                if ($data['foto'] != 'default.jpg') {
                    unlink('uploads/pemberitahuan/' . $data['foto']);
                }
                $namaFoto = $foto->getRandomName();
                $foto->move('uploads/pemberitahuan/', $namaFoto);
            }

            $data = [
                'pemberitahuan'  => $this->request->getPost('pemberitahuan'),
                'deskripsi'      => $this->request->getPost('deskripsi'),
                'tgl'            => $this->request->getPost('tgl'),
                'foto'           => $namaFoto
            ];

            $this->model->update($id, $data);
            $response = [
                'status'    => 202,
                'error'     => false,
                'message'   => 'Data berhasil diupdate.'
            ];
            return $this->respond($response, 202);
        } else {
            $response = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data tidak ditemukan.'
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
        $data = $this->model->find($id);
        if ($data) {
            if ($data['foto'] != 'default.jpg') {
                unlink('uploads/pemberitahuan/' . $data['foto']);
            }
            $this->model->delete($id);
            $response = [
                'status'    => 203,
                'error'     => false,
                'message'   => 'Data berhasil dihapus.'
            ];
            return $this->respondDeleted($response, 203);
        } else {
            $response = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data tidak ditemukan.'
            ];
            return $this->respond($response, 404);
        }
    }
}
