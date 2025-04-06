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
            'no_kk'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'KK warga harus diisi',
                ]
            ],
            'nama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama warga harus diisi'
                ]
            ],
            'jenis_kelamin'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis kelamin warga harus diisi'
                ]
            ],
            'tempat_lahir'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tempat Lahir warga harus diisi'
                ]
            ],
            'tgl_lahir'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal lahir warga harus diisi'
                ]
            ],
            'agama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Agama warga harus diisi'
                ]
            ],
            'status_nikah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Nikah warga harus diisi'
                ]
            ],
            'pendidikan'  => [
                'rules' => 'permit_empty',                
            ],
            'pekerjaan'  => [
                'rules' => 'permit_empty',
            ],
            'gaji'  => [
                'rules' => 'permit_empty',
            ],
            'nama_ayah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama ayah warga harus diisi'
                ]
            ],
            'nama_ibu'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama ibu warga harus diisi'
                ]
            ],
            'status_keluarga'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Keluarga warga harus diisi'
                ]
            ],
            'no_wa'  => [
                'rules' => 'permit_empty',
            ],
            'password'  => [
                'rules' => 'permit_empty',
            ],
            'foto'  => [
                'rules' => 'permit_empty|max_size[foto,3072]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran foto warga maksimal 3MB',
                    'is_image' => 'File yang diupload harus berupa gambar',
                    'mime_in' => 'Format foto warga harus jpg/jpeg/png'
                ]
            ],
            'status'  => [
                'rules' => 'required',
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

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName(); 
            $foto->move('uploads/warga/', $newName); 
        } else {
            $newName = null; 
        }

        $data = [
            'nik' => $this->request->getVar('nik'),
            'no_kk' => $this->request->getVar('no_kk'),
            'nama' => $this->request->getVar('nama'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'tempat_lahir' => $this->request->getVar('tempat_lahir'),
            'tgl_lahir' => $this->request->getVar('tgl_lahir'),
            'agama' => $this->request->getVar('agama'),
            'status_nikah' => $this->request->getVar('status_nikah'),
            'pendidikan' => $this->request->getVar('pendidikan'),
            'pekerjaan' => $this->request->getVar('pekerjaan'),
            'gaji' => $this->request->getVar('gaji'),
            'nama_ayah' => $this->request->getVar('nama_ayah'),
            'nama_ibu' => $this->request->getVar('nama_ibu'),
            'status_keluarga' => $this->request->getVar('status_keluarga'),
            'no_wa' => $this->request->getVar('no_wa'),
            'password' => $this->request->getVar('password') ? password_hash($this->request->getVar('password'), PASSWORD_DEFAULT) : null,
            'foto' => $newName,
            'status' => $this->request->getVar('status')
        ];

        $this->model->insert($data);

        $sekretaris = $this->pengurusModel->getByJabatan('Sekretaris');
        if ($sekretaris) {
            $whatsappSekretaris = $sekretaris['no_wa'] ?? ''; 
        } else {
            $whatsappSekretaris = '';
        }

        if ($whatsappSekretaris) {
            $this->sendNotif(
                $whatsappSekretaris,
                "Halo Sekretaris RT,..\n\nAda pendaftaran akun warga baru\nSilahkan masuk ke aplikasi untuk mengecek data dan mengkonfirmasi akun warga."
            );
        }

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
            'no_kk'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'KK warga harus diisi',
                ]
            ],
            'nama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama warga harus diisi'
                ]
            ],
            'jenis_kelamin'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis kelamin warga harus diisi'
                ]
            ],
            'tgl_lahir'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal lahir warga harus diisi'
                ]
            ],
            'agama'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Agama warga harus diisi'
                ]
            ],
            'status_nikah'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Nikah warga harus diisi'
                ]
            ],
            'pendidikan'  => [
                'rules' => 'permit_empty',
            ],
            'pekerjaan'  => [
                'rules' => 'permit_empty',
            ],
            'gaji'  => [
                'rules' => 'permit_empty',
            ],
            'status_keluarga'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Keluarga warga harus diisi'
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

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName(); 
            $foto->move('uploads/warga/', $newName); 
        } else {
            $newName = null; 
        }

        $data = [
            'nik' => $this->request->getVar('nik'),
            'no_kk' => $this->request->getVar('no_kk'),
            'nama' => $this->request->getVar('nama'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'tgl_lahir' => $this->request->getVar('tgl_lahir'),
            'agama' => $this->request->getVar('agama'),
            'status_nikah' => $this->request->getVar('status_nikah'),
            'pendidikan' => $this->request->getVar('pendidikan'),
            'pekerjaan' => $this->request->getVar('pekerjaan'),
            'gaji' => $this->request->getVar('gaji'),
            'status_keluarga' => $this->request->getVar('status_keluarga'),
            'no_wa' => $this->request->getVar('no_wa'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'foto' => $newName,
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
        if ($this->request->getVar('nik') == null){
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
        if ($this->request->getVar('nik') == null || $this->request->getVar('keterangan') == null){
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
