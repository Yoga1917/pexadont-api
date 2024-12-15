<?php

namespace App\Controllers\API;

use App\Models\PengaduanModel;
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
     
    public function __construct()
    {
        $this->PengaduanModel = new PengaduanModel();
    }
    
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
    public function jenis($jenis = null)
    {
        if ($jenis == null) {
            $data = [
                'status' => 404,
                'message' => 'failed',
                'data' => "Jenis pengaduan tidak ditemukan"
            ];
            
            return $this->respond($data, 404);
        }else{
            $data = [
                'status' => 200,
                'message' => 'success',
                'data' => $this->PengaduanModel->findByJenis($jenis)
            ];

            return $this->respond($data, 200);
        }
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
            'isi'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keluhan warga harus diisi'
                ]
            ],
            'tgl'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal dan waktu harus diisi'
                ]
            ],
            'jenis'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis pengaduan harus diisi'
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

        $foto = $this->request->getFile('foto');
        if(is_null($foto)){
            $namaFoto = null;
        } else {
            $namaFoto = $foto->getRandomName();
            $foto->move('uploads/pengaduan/', $namaFoto);
        }

        $data = [
            'nik' => $this->request->getVar('nik'),
            'isi' => $this->request->getVar('isi'),
            'foto' => $namaFoto,
            'tgl' => $this->request->getVar('tgl'),
            'jenis' => $this->request->getVar('jenis'),
        ];

        $this->PengaduanModel->insert($data);

        $response = [
            'status' => 201,
            'error' => false,
            'message' => 'Data berhasil ditambahkan'
        ];
        return $this->respondCreated($response, 201);
    }

    public function balas()
    {
        if ($this->request->getVar('id_pengaduan') == null) {
            $data = [
                'status' => 404,
                'message' => 'failed',
                'data' => "ID pengaduan tidak ditemukan"
            ];
            
            return $this->respond($data, 404);
        }else{
            $this->PengaduanModel->update($this->request->getVar('id_pengaduan'), [
                "balasan" => $this->request->getVar('balasan'),
            ]);

            $data = [
                'status' => 200,
                'message' => 'success',
                'data' => "Balasan pengaduan berhasil disimpan"
            ];

            return $this->respond($data, 200);
        }
    }
}
