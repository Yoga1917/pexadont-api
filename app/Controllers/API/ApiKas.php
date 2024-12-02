<?php

namespace App\Controllers\API;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiKas extends ResourceController
{
    protected $modelName = 'App\Models\KasModel';
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
            'bulan'         => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Bulan harus diisi.'
                ]
            ],
            'pemasukan'     => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Pemasukan harus diisi.'
                ]
            ],
            'pengeluaran'   => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Pengeluaran harus diisi.'
                ]
            ],
            'keterangan'    => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Keterangan harus diisi.'
                ]
            ],
            'tgl&waktu'     => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Tanggal dan waktu harus diisi.'
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

        $data = [
            'bulan'         => $this->request->getPost('bulan'),
            'pemasukan'     => $this->request->getPost('pemasukan'),
            'pengeluaran'   => $this->request->getPost('pengeluaran'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'tgl&waktu'     => $this->request->getPost('tgl&waktu')
        ];

        $this->model->insert($data);
        $response = [
            'status'    => 201,
            'error'     => null,
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
            'bulan'         => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Bulan harus diisi.'
                ]
            ],
            'pemasukan'     => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Pemasukan harus diisi.'
                ]
            ],
            'pengeluaran'   => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Pengeluaran harus diisi.'
                ]
            ],
            'keterangan'    => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Keterangan harus diisi.'
                ]
            ],
            'tgl&waktu'     => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Tanggal dan waktu harus diisi.'
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

        $data = [
            'bulan'         => $this->request->getPost('bulan'),
            'pemasukan'     => $this->request->getPost('pemasukan'),
            'pengeluaran'   => $this->request->getPost('pengeluaran'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'tgl&waktu'     => $this->request->getPost('tgl&waktu')
        ];

        $this->model->update($id, $data);

        $response = [
            'status'    => 202,
            'error'     => null,
            'message'   => 'Data berhasil diubah.'
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
            $response = [
                'status'    => 203,
                'error'     => null,
                'message'   => 'Data berhasil dihapus.'
            ];

            return $this->respondDeleted($response, 203);
        } else {
            return $this->failNotFound('Data tidak ditemukan dengan id ' . $id);
        }
    }
}
