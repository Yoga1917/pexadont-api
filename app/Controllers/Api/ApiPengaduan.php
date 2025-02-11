<?php

namespace App\Controllers\API;

use App\Models\PengaduanModel;
use App\Models\PengurusModel;
use App\Models\WargaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiPengaduan extends ResourceController
{
    protected $format    = 'json';
    protected $PengaduanModel;
    protected $PengurusModel;
    protected $WargaModel;
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
     
    public function __construct()
    {
        $this->PengaduanModel = new PengaduanModel();
        $this->PengurusModel = new PengurusModel();
        $this->WargaModel = new WargaModel();
    }
    
    public function index()
    {
        $pengaduan = $this->PengaduanModel->getPengaduanLengkap();

        $response = [
            'status'    => 200,
            'message'   => 'Success',
            'data'      => $pengaduan
        ];

        return $this->respond($response, 200);
    }
    
    public function warga($nik = null)
    {   
        if ($nik == null) {
            $data = [
                'status' => 404,
                'message' => 'failed',
                'data' => "NIK warga tidak ditemukan"
            ];
            
            return $this->respond($data, 404);
        } else {
            // Ambil semua pengaduan warga berdasarkan NIK
            $pengaduans = $this->PengaduanModel->where('nik', $nik)->get()->getResultArray();
            $pengaduanFixs = [];

            foreach ($pengaduans as $p) {
                // Ambil ID pengurus yang membalas pengaduan
                $idPengurus = $p['id_pengurus']; 

                $pengurusData = $this->PengurusModel->where('id_pengurus', $idPengurus)->first();

                if ($pengurusData) {
                    // Dapatkan NIK pengurus dari tabel pengurus
                    $nikPengurus = $pengurusData['nik'];
                
                    // Cari data pengurus dari tabel warga berdasarkan NIK yang ditemukan
                    $pengurus = $this->WargaModel->where('nik', $nikPengurus)->first();
                    
                    array_push($pengaduanFixs, [
                        ...$p,
                        "aksiBy" => $pengurus ? $pengurus['nama'] : "Data pengurus tidak ditemukan.",
                        "jabatanAksiBy" => $pengurusData['jabatan'],
                        'fotoAksiBy' => $pengurus ? $pengurus['foto'] : null
                    ]);
                } else {
                    array_push($pengaduanFixs, [
                        ...$p,
                        "aksiBy" => "Data pengurus tidak ditemukan.",
                        "jabatanAksiBy" => "Tidak diketahui",
                        'fotoAksiBy' => null
                    ]);
                }
            }

            $data = [
                'status' => 200,
                'message' => 'success',
                'data' => $pengaduanFixs
            ];

            return $this->respond($data, 200);
        }
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
            $aksiBy = [
                "Kinerja" => $this->PengurusModel->getByJabatan("Ketua RT"),
                "Fasilitas" => $this->PengurusModel->getByJabatan("Ketua RT"),
                "Kegiatan" => $this->PengurusModel->getByJabatan("Sekretaris"),
                "Keuangan" => $this->PengurusModel->getByJabatan("Bendahara"),
                "Kebersihan" => $this->PengurusModel->getByJabatan("Kordinator Kebersihan"),
                "Keamanan" => $this->PengurusModel->getByJabatan("Kordinator Keamanan"),
            ];
            
            $pengaduans = $this->PengaduanModel->findByJenis($jenis);
            $pengaduanFixs = [];
            foreach ($pengaduans as $p) {
                array_push($pengaduanFixs, [
                    ...$p,
                    "aksiBy" => $aksiBy[$p['jenis']]['nama'] ." (". $aksiBy[$p['jenis']]['jabatan'] . ")",
                    "fotoAksiBy" => $aksiBy[$p['jenis']]['foto']
                ]);
            }

            $data = [
                'status' => 200,
                'message' => 'success',
                'data' => $pengaduanFixs
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
        $this->sendWhatsapp($data['jenis']);

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
            $id_pengurus = $this->request->getVar('id_pengurus') ?? 0;
            // update
            $this->PengaduanModel->update($this->request->getVar('id_pengaduan'), [
                "balasan" => $this->request->getVar('balasan'),
                "id_pengurus" => $id_pengurus,
            ]);
            
            // notif
            $pengaduan = $this->PengaduanModel->find($this->request->getVar('id_pengaduan'));
            $whatsapp = $this->WargaModel->find($pengaduan['nik'])['no_wa'];
            $this->sendNotif(
                $whatsapp,
                "Pengaduan anda di aplikasi Pexadont sudah dibalas...\nJenis pengaduan : ".$pengaduan['jenis']."\nTanggal Pengaduan : ".$pengaduan['tgl']."\nBalasan : " . $this->request->getVar('balasan')
            );

            // respond
            $data = [
                'status' => 200,
                'message' => 'success',
                'data' => "Balasan pengaduan berhasil disimpan"
            ];
            return $this->respond($data, 200);
        }
    }

    
    // helper func send whatsapp
    private function sendWhatsapp($jenis_pengaduan){
        switch ($jenis_pengaduan) {
            case 'Kinerja':
                $pengurus = $this->PengurusModel->where('jabatan', 'Ketua RT')->get()->getRowArray();
                if(is_null($pengurus)){
                    return;
                }

                $whatsapp = $this->PengurusModel->relasiWargaBynik($pengurus['nik'])['no_wa'];
                $this->sendNotif($whatsapp, "Notifikasi Pengaduan Baru...\nJenis pengaduan : Kinerja\n\nSegera cek aplikasi pexadont RT 19");
                break;
            case 'Fasilitas':
                $pengurus = $this->PengurusModel->where('jabatan', 'Ketua RT')->get()->getRowArray();
                if(is_null($pengurus)){
                    return;
                }

                $whatsapp = $this->PengurusModel->relasiWargaBynik($pengurus['nik'])['no_wa'];
                $this->sendNotif($whatsapp, "Notifikasi Pengaduan Baru...\nJenis pengaduan : Fasilitas\n\nSegera cek aplikasi pexadont RT 19");
                break;
            case 'Kegiatan':
                $pengurus = $this->PengurusModel->where('jabatan', 'Sekretaris')->get()->getRowArray();
                if(is_null($pengurus)){
                    return;
                }

                $whatsapp = $this->PengurusModel->relasiWargaBynik($pengurus['nik'])['no_wa'];
                $this->sendNotif($whatsapp, "Notifikasi Pengaduan Baru...\nJenis pengaduan : Kegiatan\n\nSegera cek aplikasi pexadont RT 19");
                break;
            case 'Keuangan':
                $pengurus = $this->PengurusModel->where('jabatan', 'Bendahara')->get()->getRowArray();
                if(is_null($pengurus)){
                    return;
                }

                $whatsapp = $this->PengurusModel->relasiWargaBynik($pengurus['nik'])['no_wa'];
                $this->sendNotif($whatsapp, "Notifikasi Pengaduan Baru...\nJenis pengaduan : Keuangan\n\nSegera cek aplikasi pexadont RT 19");
                break;
            case 'Kebersihan':
                $pengurus = $this->PengurusModel->where('jabatan', 'Kordinator Kebersihan')->get()->getRowArray();
                if(is_null($pengurus)){
                    return;
                }

                $whatsapp = $this->PengurusModel->relasiWargaBynik($pengurus['nik'])['no_wa'];
                $this->sendNotif($whatsapp, "Notifikasi Pengaduan Baru...\nJenis pengaduan : Kebersihan\n\nSegera cek aplikasi pexadont RT 19");
                break;
            case 'Keamanan':
                $pengurus = $this->PengurusModel->where('jabatan', 'Kordinator Keamanan')->get()->getRowArray();
                if(is_null($pengurus)){
                    return;
                }

                $whatsapp = $this->PengurusModel->relasiWargaBynik($pengurus['nik'])['no_wa'];
                $this->sendNotif($whatsapp, "Notifikasi Pengaduan Baru...\nJenis pengaduan : Keamanan\n\nSegera cek aplikasi pexadont RT 19");
                break;
            default:
                return;
                break;
        }
    }

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
