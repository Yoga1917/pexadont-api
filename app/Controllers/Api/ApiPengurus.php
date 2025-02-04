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
        $nik = $this->request->getPost('nik');
        $jabatan = $this->request->getPost('jabatan');
        $periode = $this->request->getPost('periode');

        $cekNik = $this->PengurusModel
            ->where('nik', $nik)
            ->where('periode', $periode)
            ->findAll();

        if (count($cekNik) > 0) {
            return $this->respond(['status' => 400, 'error' => true, 'message' => 'Pengurus dengan NIK ini sudah ada di periode tersebut'], 400);
        }

        $cekJabatan = $this->PengurusModel
            ->where('jabatan', $jabatan)
            ->where('periode', $periode)
            ->findAll();

        if (count($cekJabatan) > 0) {
            return $this->respond(['status' => 400, 'error' => true, 'message' => 'Jabatan ini sudah ada di periode tersebut'], 400);
        }

        $data = [
            'nik' => $nik,
            'jabatan' => $jabatan,
            'periode' => $periode,
            'status_pengurus' => 1 
        ];

        $this->PengurusModel->insert($data);
        return $this->respond(['status' => 201, 'message' => 'Pengurus berhasil ditambahkan'], 201);
    }

    public function updateStatus()
    {
        $nik = $this->request->getVar('nik');
        $periode = $this->request->getVar('periode');
        $status_pengurus = $this->request->getVar('status_pengurus');

        if (!$nik || !$periode) {
            return $this->response->setJSON(['status' => false, 'message' => 'NIK dan Periode harus diisi']);
        }

        $this->PengurusModel->updateStatusByNikPeriode($nik, $periode, $status_pengurus);

        return $this->response->setJSON(['status' => true, 'message' => 'Status pengurus berhasil diperbarui']);
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

    public function login()
    {
        $nik = $this->request->getPost('nik');
        $password = $this->request->getPost('password');

        $pengurus = $this->PengurusModel->getActivePengurus($nik); // (5) Hanya mengambil pengurus aktif

        if (!$pengurus) {
            return $this->respond(['status' => 403, 'error' => true, 'message' => 'Anda bukan pengurus aktif'], 403);
        }

        if (!password_verify($password, $pengurus['password'])) {
            return $this->respond(['status' => 401, 'error' => true, 'message' => 'Password salah'], 401);
        }

        return $this->respond(['status' => 200, 'message' => 'Login berhasil', 'data' => $pengurus], 200);
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
