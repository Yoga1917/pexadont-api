<?php

namespace App\Controllers\API;

use App\Models\PengurusModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiPemberitahuan extends ResourceController
{
    protected $modelName = 'App\Models\PemberitahuanModel';
    protected $format    = 'json';
    protected $pengurusModel;
    
    public function __construct()
    {
        $this->pengurusModel = new PengurusModel();
    }

    public function index()
    {
        $aksiBy = $this->pengurusModel->getByJabatan("Sekretaris");
        $data = [
            'status' => 200,
            'error' => false,
            'data' => $this->model->orderBy('tgl', 'desc')->get()->getResultArray(),
            'aksiBy' => $aksiBy['nama'] ." (". $aksiBy['jabatan'] . ")",
            'fotoAksiBy' => $aksiBy['foto'] // ini yang ditambahkan
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
            ]

        ])) {
            $response = [
                'status' => 400,
                'error' => true,
                'data' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $file = $this->request->getFile('file');
        if(is_null($file)){
            $namaFile = null;
        } else {
            $namaFile = $file->getRandomName();
            $file->move('uploads/pemberitahuan/', $namaFile);
        }

        $data = [
            'pemberitahuan'  => $this->request->getVar('pemberitahuan'),
            'deskripsi'      => $this->request->getVar('deskripsi'),
            'tgl'            => date('Y-m-d'),
            'file'           => $namaFile
        ];

        $this->model->insert($data);
        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Pemberitahuan berhasil ditambahkan'
        ];
        return $this->respond($response, 200);
    }
}
