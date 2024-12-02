<?php

namespace App\Controllers\API;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiPengurus extends ResourceController
{
    protected $PengurusModel;
    protected $WargaModel;
    protected $format = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'status' => 200,
            'error' => false,
            'message' => 'Data Pengurus Berhasil Diambil',
            'data' => $this->PengurusModel->relasiWarga()
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
            'error' => false,
            'message' => 'Data Warga Berhasil Diambil',
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
            'nik'   => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIK Wajib Diisi',
                ]
            ],
            'jabatan'   => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jabatan Wajib Diisi',
                ]
            ]
        ])) {
            $response = [
                'status' => 400,
                'error' => true,
                'message' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $nik = $this->WargaModel->find($this->request->getPost('nik'));
        if (empty($nik)) {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'NIK Tidak Ditemukan'
            ];
            return $this->respond($response, 404);
        }

        $data = [
            'nik' => $this->request->getPost('nik'),
            'jabatan' => $this->request->getPost('jabatan')
        ];

        $this->PengurusModel->insert($data);
        $response = [
            'status' => 201,
            'error' => false,
            'message' => 'Data Pengurus Berhasil Ditambahkan',
            'data' => $data
        ];
        return $this->respond($response, 201);
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
        $data = $this->PengurusModel->find($id);
        if (empty($data)) {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'Data Pengurus Tidak Ditemukan'
            ];
            return $this->respond($response, 404);
        }
        $response = [
            'status' => 200,
            'error' => false,
            'message' => 'Data Pengurus Berhasil Diambil',
            'data' => $data,
            'warga' => $this->WargaModel->findAll()
        ];
        return $this->respond($response, 200);
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
            'nik'   => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIK Wajib Diisi',
                ]
            ],
            'jabatan'   => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jabatan Wajib Diisi',
                ]
            ]
        ])) {
            $response = [
                'status' => 400,
                'error' => true,
                'message' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $nik = $this->WargaModel->find($this->request->getPost('nik'));
        if (empty($nik)) {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'NIK Tidak Ditemukan'
            ];
            return $this->respond($response, 404);
        }

        $data = $this->PengurusModel->find($id);
        if ($data) {
            $data = [
                'nik' => $this->request->getPost('nik'),
                'jabatan' => $this->request->getPost('jabatan')
            ];
            $this->PengurusModel->update($id, $data);
            $response = [
                'status' => 202,
                'error' => false,
                'message' => 'Data Pengurus Berhasil Diubah',
                'data' => $data
            ];
            return $this->respond($response, 202);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'Data Pengurus Tidak Ditemukan'
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
        $data = $this->PengurusModel->find($id);
        if ($data) {
            $this->PengurusModel->delete($id);
            $response = [
                'status' => 203,
                'error' => false,
                'message' => 'Data Pengurus Berhasil Dihapus'
            ];
            return $this->respond($response, 203);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'message' => 'Data Pengurus Tidak Ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }
}
