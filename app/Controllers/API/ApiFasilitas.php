<?php

namespace App\Controllers\API;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiFasilitas extends ResourceController
{
    protected $modelName = 'App\Models\FasilitasModel';
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
            'message'   => 'Success',
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
            'nama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama fasilitas harus diisi.'
                ]
            ],
            'jml'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jumlah fasilitas harus diisi.'
                ]
            ],
            'foto'  => [
                'rules' => 'max_size[fasilitas.foto,3072]|is_image[fasilitas.foto]|mime_in[fasilitas.foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran foto fasilitas maksimal 3MB.',
                    'is_image' => 'Yang anda pilih bukan gambar.',
                    'mime_in'  => 'Format gambar harus jpg/jpeg/png.'
                ]
            ],
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status fasilitas harus diisi.'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'error'     => $this->validator->getErrors(),
                'message'   => 'Data yang dikirimkan tidak lengkap.'
            ];

            return $this->respond($response, 400);
        }

        if ($this->request->getFile('foto') != null) {
            $file = $this->request->getFile('foto');
            $namaFoto = $file->getRandomName();
            $file->move('uploads/fasilitas/', $namaFoto);
            $data = [
                'nama'      => $this->request->getPost('nama'),
                'jml'       => $this->request->getPost('jml'),
                'foto'      => $namaFoto,
                'status'    => $this->request->getPost('status')
            ];
            return $this->model->insert($data);
            $response = [
                'status'    => 201,
                'error'     => null,
                'message'   => 'Data berhasil disimpan.'
            ];

            return $this->respondCreated($response, 201);
        }

        $data = [
            'nama'      => $this->request->getPost('nama'),
            'jml'       => $this->request->getPost('jml'),
            'foto'      => null,
            'status'    => $this->request->getPost('status')
        ];
        return $this->model->insert($data);
        $response = [
            'status'    => 201,
            'error'     => null,
            'message'   => 'Data berhasil disimpan. (tanpa foto)'
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
            return $this->failNotFound('Data tidak ditemukan dengan id ' . $id);
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
            'nama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama fasilitas harus diisi.'
                ]
            ],
            'jml'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jumlah fasilitas harus diisi.'
                ]
            ],
            'foto'  => [
                'rules' => 'max_size[fasilitas.foto,3072]|is_image[fasilitas.foto]|mime_in[fasilitas.foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran foto fasilitas maksimal 3MB.',
                    'is_image' => 'Yang anda pilih bukan gambar.',
                    'mime_in'  => 'Format gambar harus jpg/jpeg/png.'
                ]
            ],
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status fasilitas harus diisi.'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'errors'    => $this->validator->getErrors(),
                'message'   => 'Data yang dikirimkan tidak lengkap.'
            ];
            return $this->respond($response, 400);
        }

        if ($this->request->getFile('foto') != null) {
            $file = $this->request->getFile('foto');
            $namaFoto = $file->getRandomName();
            $file->move('uploads/fasilitas/', $namaFoto);
            $id = $this->model->find($id);
            if ($id['foto'] != null) {
                unlink('uploads/fasilitas/' . $id['foto']);
            }
        }

        $updateFoto = ($this->request->getFile('foto') != null) ? $namaFoto : $id['foto'];

        $data = [
            'nama'      => $this->request->getRawInput('nama'),
            'jml'       => $this->request->getRawInput('jml'),
            'foto'      => $updateFoto,
            'status'    => $this->request->getRawInput('status')
        ];

        $this->model->update($id, $data);
        $response = [
            'status'    => 202,
            'error'     => null,
            'message'   => 'Data berhasil diupdate.'
        ];

        return $this->respond($response, 202);
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
            $this->model->delete($id);
            if ($data['foto'] != null) {
                unlink('uploads/fasilitas/' . $data['foto']);
            }
            $response = [
                'status'    => 203,
                'message'   => 'Data berhasil dihapus.'
            ];

            return $this->respondDeleted($response, 203);
        } else {
            return $this->failNotFound('Data tidak ditemukan dengan id ' . $id);
        }
    }
}
