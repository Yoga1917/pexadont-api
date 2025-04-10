<?php

namespace App\Controllers\API;

use App\Models\KeluargaModel;
use App\Models\WargaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiKeluarga extends ResourceController
{
    protected $KeluargaModel;
    protected $WargaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->KeluargaModel    = new KeluargaModel();
        $this->WargaModel       = new WargaModel();
    }
    
    public function index()
    {
        $keluarga = $this->KeluargaModel->relasiWarga();

        $data = [
            'status'    => 200,
            'error'     => false,
            'data'      => $keluarga
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if (!$this->validate([
            'no_kk'  => [
                'rules' => 'required|is_unique[keluarga.no_kk]',
                'errors' => [
                    'required' => 'KK warga harus diisi',
                    'is_unique' => 'KK warga sudah terdaftar'
                ]
            ],
            'nik' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'NIK Warga Harus Diisi',
                ]
            ],
            'no_rumah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor rumah warga harus diisi'
                ]
            ],
            'alamat' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Alamat Harus Diisi',
                ]
            ],
            'latitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Lokasi Harus Diisi',
                ]
            ],
            'longitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Lokasi Harus Diisi',
                ]
            ],
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status harus diisi.'
                ]
            ]
        ])) {
            $data = [
                'status'    => 400,
                'error'     => true,
                'message'   => $this->validator->getErrors()
            ];

            return $this->respond($data, 400);
        }

        $data = [
            'no_kk'       => $this->request->getPost('no_kk'),
            'nik'         => $this->request->getPost('nik'),
            'no_rumah'    => $this->request->getPost('no_rumah'),
            'alamat'      => $this->request->getPost('alamat'),
            'latitude'    => $this->request->getPost('latitude'),
            'longitude'   => $this->request->getPost('longitude'),
            'status'      => $this->request->getPost('status'),
        ];

        $this->KeluargaModel->insert($data);
        $response = [
            'status'    => 201,
            'error'     => false,
            'message'   => 'Data Keluarga Berhasil Disimpan.'
        ];

        return $this->respondCreated($response, 200);
    }

    public function edit($id = null)
    {
        $data = $this->KeluargaModel->find($id);
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
                'data' => 'KK tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'nik' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'NIK Warga Harus Diisi',
                ]
            ],
            'no_rumah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor rumah warga harus diisi'
                ]
            ],
            'alamat' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Alamat Harus Diisi',
                ]
            ],
            'latitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Lokasi Harus Diisi',
                ]
            ],
            'longitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Lokasi Harus Diisi',
                ]
            ],
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status harus diisi.'
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
            'nik'         => $this->request->getPost('nik'),
            'no_rumah'    => $this->request->getPost('no_rumah'),
            'alamat'      => $this->request->getPost('alamat'),
            'latitude'    => $this->request->getPost('latitude'),
            'longitude'   => $this->request->getPost('longitude'),
            'status'      => $this->request->getPost('status'),
        ];

        $this->KeluargaModel->update($id, $data);
        $response = [
            'status' => 202,
            'error' => false,
            'data' => 'KK berhasil diupdate'
        ];
        return $this->respond($response, 202);
    }

    public function terima(){
        if ($this->request->getVar('no_kk') == null){
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Keluarga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
        // update status
        $this->KeluargaModel->update($this->request->getVar('no_kk'), ['status' => "Menetap"]);

        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Pendaftaran Keluarga berhasil diterima'
        ];
        return $this->respond($response, 200);
    }

    public function tolak(){
        if ($this->request->getVar('no_kk') == null){
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Data Nomor Kepala Keluarga diperlukan!'
            ];
            return $this->respond($response, 404);
        }

        // delete Keluarga
        $this->KeluargaModel->delete($this->request->getVar('no_kk'));

        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Pendaftaran Keluarga ditolak'
        ];
        return $this->respond($response, 200);
    }
}
