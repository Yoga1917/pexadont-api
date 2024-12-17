<?php

namespace App\Controllers\API;

use App\Models\RkbModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiRkb extends ResourceController
{
    protected $format    = 'json';
    protected $rkbModel;
    
    public function __construct()
    {
        $this->rkbModel = new RkbModel();
    }
    
    public function index()
    {
        $tahun = $this->request->getVar('tahun') ?? date('Y');
        $bulans = [
            ["date" => "12", "name" => "Desember"],
            ["date" => "11", "name" => "November"],
            ["date" => "10", "name" => "Oktober"],
            ["date" => "09", "name" => "September"],
            ["date" => "08", "name" => "Agustus"],
            ["date" => "07", "name" => "Juli"],
            ["date" => "06", "name" => "Juni"],
            ["date" => "05", "name" => "Mei"],
            ["date" => "04", "name" => "April"],
            ["date" => "03", "name" => "Maret"],
            ["date" => "02", "name" => "Februari"],
            ["date" => "01", "name" => "Januari"]
        ];

        $datas = [];
        foreach ($bulans as $bulan) {
            array_push(
                $datas,
                [
                    "bulan" => $bulan['name'] ." ". $tahun,
                    "data" => $this->rkbModel->where('year(tgl)', $tahun)
                                             ->where('month(tgl)', date("m",strtotime($tahun."-".$bulan['date'])))
                                             ->get()
                                             ->getResultArray()
                ]
            );
        }

        $data = [
            'status'        => 200,
            'message'       => 'success',
            'data'          => $datas
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if (!$this->validate([
            'tgl'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal harus diisi'
                ]
            ],
            'keterangan'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Keterangan harus diisi'
                ]
            ]
        ])) {
            $response = [
                'status'    => 400,
                'errors'    => true,
                'message'   => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $data = [
            'tgl' => $this->request->getPost('tgl'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        $this->rkbModel->insert($data);
        $response = [
            'status'    => 201,
            'error'     => false,
            'message'   => "Data berhasil disimpan"
        ];

        return $this->respondCreated($response, 201);
    }
}
