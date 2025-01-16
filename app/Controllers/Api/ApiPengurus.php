<?php

namespace App\Controllers\API;

use App\Models\PengurusModel;
use App\Models\WargaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiPengurus extends ResourceController
{
    protected $PengurusModel;
    protected $WargaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->PengurusModel = new PengurusModel();
        $this->WargaModel = new WargaModel();
    }

    public function index()
    {
        $currentPeriode = "2024-2029";
        $periode = $this->request->getVar('periode') ??  $currentPeriode;

        $data = [
            'status' => 200,
            'error' => false,
            'message' => 'Data Pengurus Berhasil Diambil',
            'data' => $this->PengurusModel->relasiWarga($periode)
        ];
        return $this->respond($data, 200);
    }

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

        $cekNik = $this->PengurusModel->where('nik', $this->request->getPost('nik'))->where('periode', $this->request->getPost('periode'))->get()->getResultArray();
        if(count($cekNik) > 0){
            $response = [
                'status' => 400,
                'error' => true,
                'data' => 'Pengurus dengan NIK di periode tersebut sudah tersedia'
            ];
            return $this->respond($response, 400);
        }
        
        $cekJabatan = $this->PengurusModel->where('jabatan', $this->request->getPost('jabatan'))->where('periode', $this->request->getPost('periode'))->get()->getResultArray();
        if(count($cekJabatan) > 0){
            $response = [
                'status' => 400,
                'error' => true,
                'data' => 'Pengurus dengan jabatan '.$this->request->getPost('jabatan').' di periode tersebut sudah tersedia'
            ];
            return $this->respond($response, 400);
        }        

        $data = [
            'nik' => $this->request->getPost('nik'),
            'jabatan' => $this->request->getPost('jabatan'),
            'periode' => $this->request->getPost('periode'),
            'status_pengurus' => 1 // ini yang ditambahkan
        ];
        $this->PengurusModel->insert($data);

        $whatsapp = $this->WargaModel->find($this->request->getPost('nik'))['no_wa'];
        $this->sendNotif(
            $whatsapp,
            "Notifikasi Kepengurusan Baru\n\nAnda sekarang terdaftar sebagai pengurus di aplikasi Pexadont RT 19.\nJabatan : " . $data['jabatan'] . "\nPeriode : " . $data['periode']
        );

        $response = [
            'status' => 201,
            'error' => false,
            'data' => 'Data Pengurus Berhasil Ditambahkan',
        ];
        return $this->respond($response, 201);
    }

    public function updatePengurus($nik = null) // ini yang ditambahkan
    {
        $data = $this->PengurusModel->where('nik', $nik)->get()->getRowArray();
        if(empty($data)) {
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Data Pengurus tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }

        $status = $data['status_pengurus'] == 1 ? 2 : 0;
        $this->PengurusModel->update($data['id_pengurus'], ['status_pengurus' => $status]);

        $response = [
            'status' => 200,
            'error' => false,
            'data' => 'Status Pengurus Berhasil Diubah',
        ];
        return $this->respond($response, 200);
    }

    public function show($nik = null)
    {
        $data = $this->PengurusModel->relasiWargaBynik($nik);
        if (empty($data)) {
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Data Pengurus Tidak Ditemukan'
            ];
            return $this->respond($response, 404);
        }
        $response = [
            'status' => 200,
            'error' => false,
            'data' => $data
        ];
        return $this->respond($response, 200);
    }

    // notif
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
