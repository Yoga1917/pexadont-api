<?php

namespace App\Controllers\API;

use App\Models\PengurusModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiFasilitas extends ResourceController
{
    protected $modelName = 'App\Models\FasilitasModel';
    protected $pengurusModel;
    protected $format    = 'json';
 
    public function __construct()
    {
        $this->pengurusModel = new PengurusModel();
    }

    public function index()
    {
        $aksiBy = $this->pengurusModel->getByJabatan("Ketua RT");

        $data = [
            'status'    => 200,
            'message'   => 'Success',
            'data'      => $this->model->findAll(),
            'aksiBy'    => $aksiBy['nama'] ." (". $aksiBy['jabatan'] . ")",
        ];

        return $this->respond($data, 200);
    }

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
                    'required' => 'Jumlah harus diisi.'
                ]
            ],
            'foto'  => [
                'rules' => 'uploaded[foto]|max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Foto Fasilitas harus diisi',
                    'max_size' => 'Ukuran foto Fasilitas maksimal 3MB',
                    'is_image' => 'File yang diupload harus berupa gambar',
                    'mime_in' => 'Format foto Fasilitas harus jpg/jpeg/png'
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
                'status' => 400,
                'error'  => true,
                'data'   => $this->validator->getErrors()
            ];

            return $this->respond($response, 400);
        }

        $foto = $this->request->getFile('foto');
        $newName = $foto->getRandomName();
        $foto->move('uploads/fasilitas/', $newName);

        $data = [
            'nama'      => $this->request->getVar('nama'),
            'jml'       => $this->request->getVar('jml'),
            'foto'      => $newName,
            'status'    => $this->request->getVar('status')
        ];
        $this->model->insert($data);
        
        $response = [
            'status'    => 200,
            'error'     => false,
            'data'   => 'Data berhasil disimpan.'
        ];

        return $this->respondCreated($response, 200);
    }

    public function edit($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            $response = [
                'status' => 200,
                'error' => false,
                'data' => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Fasilitas tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }

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
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status fasilitas harus diisi.'
                ]
            ]
        ])) {
            $response = [
                'status' => 400,
                'error' => true,
                'data' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $data = [
            'nama'      => $this->request->getVar('nama'),
            'jml'       => $this->request->getVar('jml'),
            'status'    => $this->request->getVar('status')
        ];

        $this->model->update($id, $data);
        $response = [
            'status' => 202,
            'error' => false,
            'data' => 'Fasilitas berhasil diupdate'
        ];
        return $this->respond($response, 202);
    }
}
