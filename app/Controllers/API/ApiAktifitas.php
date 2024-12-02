<?php

namespace App\Controllers\API;

use App\Models\AktifitasModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiAktifitas extends ResourceController
{
    protected $format    = 'json';
    protected $aktifitasModel;
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function __construct()
    {
        $this->aktifitasModel = new AktifitasModel();
    }
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'status'        => 200,
            'message'       => 'Success',
            'data'          => $this->aktifitasModel->findAll()
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
                    'required' => 'Nama aktifitas harus diisi'
                ]
            ],
            'deskripsi'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi aktifitas harus diisi'
                ]
            ],
            'keterangan'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keterangan aktifitas harus diisi'
                ]
            ],
            'tgl&waktu'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal dan waktu aktifitas harus diisi'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'errors'    => $this->validator->getErrors(),
                'message'   => [
                    'error' => 'Data yang dikirim tidak valid'
                ]
            ];
            return $this->respond($response, 400);
        }

        $data = [
            'nama'          => $this->request->getPost('nama'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'tgl&waktu'     => $this->request->getPost('tgl&waktu')
        ];

        $this->aktifitasModel->insert($data);
        $response = [
            'status'    => 201,
            'error'     => null,
            'message'   => [
                'success' => 'Data berhasil disimpan'
            ]
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
        $data = $this->aktifitasModel->find($id);

        if ($data) {
            return $this->respond([
                'status'    => 200,
                'message'   => 'Success',
                'data'      => $data
            ]);
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
                    'required' => 'Nama aktifitas harus diisi'
                ]
            ],
            'deskripsi'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Deskripsi aktifitas harus diisi'
                ]
            ],
            'keterangan'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keterangan aktifitas harus diisi'
                ]
            ],
            'tgl&waktu'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal dan waktu aktifitas harus diisi'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'errors'    => $this->validator->getErrors(),
                'message'   => [
                    'error' => 'Data yang dikirim tidak valid'
                ]
            ];
            return $this->respond($response, 400);
        }

        $data = [
            'nama'          => $this->request->getPost('nama'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'tgl&waktu'     => $this->request->getPost('tgl&waktu')
        ];

        $this->aktifitasModel->update($id, $data);

        $response = [
            'status'    => 202,
            'error'     => null,
            'message'   => [
                'success' => 'Data berhasil diupdate'
            ]
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
        $data = $this->aktifitasModel->find($id);

        if ($data) {
            $this->aktifitasModel->delete($id);
            $response = [
                'status'    => 203,
                'message'   => [
                    'success' => 'Data berhasil dihapus'
                ]
            ];

            return $this->respondDeleted($response, 203);
        } else {
            return $this->failNotFound('Data tidak ditemukan dengan id ' . $id);
        }
    }
}