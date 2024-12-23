<?php

namespace App\Controllers\API;

use App\Models\PengurusModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiWarga extends ResourceController
{
    protected $modelName = 'App\Models\WargaModel';
    protected $format    = 'json';
    protected $pengurusModel;
    
    public function __construct()
    {
        $this->pengurusModel = new PengurusModel();
    }

    public function index()
    {
        $data = [
            'status' => 200,
            'error' => false,
            'data' => $this->model->findAll()
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if (!$this->validate([
            'nik'  => [
                'rules' => 'required|is_unique[warga.nik]',
                'errors' => [
                    'required' => 'NIK warga harus diisi',
                    'is_unique' => 'NIK warga sudah terdaftar'
                ]
            ],
            'nama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama warga harus diisi'
                ]
            ],
            'tgl_lahir'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal lahir warga harus diisi'
                ]
            ],
            'jenis_kelamin'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis kelamin warga harus diisi'
                ]
            ],
            'no_rumah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor rumah warga harus diisi'
                ]
            ],
            'no_wa'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor whatsapp warga harus diisi'
                ]
            ],
            'password'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password warga harus diisi'
                ]
            ],
            'foto'  => [
                'rules' => 'uploaded[foto]|max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Foto warga harus diisi',
                    'max_size' => 'Ukuran foto warga maksimal 3MB',
                    'is_image' => 'File yang diupload harus berupa gambar',
                    'mime_in' => 'Format foto warga harus jpg/jpeg/png'
                ]
            ],
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status warga harus diisi'
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

        $foto = $this->request->getFile('foto');
        $newName = $foto->getRandomName();
        $foto->move('uploads/warga/', $newName);

        $data = [
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'tgl_lahir' => $this->request->getVar('tgl_lahir'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'no_rumah' => $this->request->getVar('no_rumah'),
            'no_wa' => $this->request->getVar('no_wa'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'foto' => $newName,
            'status' => $this->request->getVar('status')
        ];

        $this->model->insert($data);
        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Warga berhasil ditambahkan'
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
                'data' => 'Warga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }
    
    public function update($id = null)
    {
        if (!$this->validate([
            'nik'  => [
                'rules' => 'required|is_unique[warga.nik,nik,' . $id . ']',
                'errors' => [
                    'required' => 'NIK warga harus diisi',
                    'is_unique' => 'NIK warga sudah terdaftar'
                ]
            ],
            'nama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama warga harus diisi'
                ]
            ],
            'tgl_lahir'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal lahir warga harus diisi'
                ]
            ],
            'jenis_kelamin'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis kelamin warga harus diisi'
                ]
            ],
            'no_rumah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor rumah warga harus diisi'
                ]
            ],
            'no_wa'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor whatsapp warga harus diisi'
                ]
            ],
            'status'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status warga harus diisi'
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
            'nik' => $this->request->getVar('nik'),
            'nama' => $this->request->getVar('nama'),
            'tgl_lahir' => $this->request->getVar('tgl_lahir'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'no_rumah' => $this->request->getVar('no_rumah'),
            'no_wa' => $this->request->getVar('no_wa'),
            'status' => $this->request->getVar('status')
        ];

        $this->model->update($id, $data);
        $response = [
            'status' => 202,
            'error' => false,
            'data' => 'Warga berhasil diupdate'
        ];
        return $this->respond($response, 202);
    }

    public function terima(){
        if (!$this->validate([
            'nik'  => ['rules' => 'required'],
        ])) {
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'NIK warga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
        // update status
        $this->model->update($this->request->getVar('nik'), ['status' => 1]);
        // send notif
        $warga = $this->model->find($this->request->getVar('nik'));
        $this->sendNotif($warga['no_wa'], "Halo ".$warga['nama']."...\nPendaftaran anda di aplikasi Pexadont sudah diterima, sekarang anda sudah bisa login dan akses semua fitur.");

        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Pendaftaran warga berhasil diterima'
        ];
        return $this->respond($response, 200);
    }

    public function tolak(){
        if (!$this->validate([
            'nik'  => ['rules' => 'required'],
            'keterangan'  => ['rules' => 'required'],
        ])) {
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Data NIK dan keterangan penolakan pendaftaran diperlukan'
            ];
            return $this->respond($response, 404);
        }
        // send notif
        $warga = $this->model->find($this->request->getVar('nik'));
        $this->sendNotif(
            $warga['no_wa'],
            "Halo ".$warga['nama']."...\nPendaftaran anda di aplikasi Pexadont ditolak.\n\nKeterangan :\n" . $this->request->getVar('keterangan')
        );
        // delete warga
        $this->model->delete($this->request->getVar('nik'));

        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Pendaftaran warga ditolak'
        ];
        return $this->respond($response, 200);
    }

    // helper func
    private function sendNotif($whatsapp, $text){
        $token = "csVhjZFrHjuVWVwiZsRm";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
            'target' => $whatsapp,
            'message' => $text,
            'countryCode' => '62',
        ),
            CURLOPT_HTTPHEADER => array('Authorization: ' . $token),
        ));

        curl_exec($curl);
        curl_close($curl);
	}
}