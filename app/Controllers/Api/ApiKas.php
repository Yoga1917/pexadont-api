<?php

namespace App\Controllers\API;

use App\Models\PemasukanModel;
use App\Models\PengeluaranModel;
use App\Models\PengurusModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiKas extends ResourceController
{
    protected $modelName = 'App\Models\KasModel';
    protected $format    = 'json';
    protected $pemasukanModel;
    protected $pengeluaranModel;
    protected $pengurusModel;
    
    public function __construct()
    {
        $this->pemasukanModel = new PemasukanModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->pengurusModel = new PengurusModel();
    }

    public function index()
    {
        $aksiBy = $this->pengurusModel->getByJabatan("Bendahara");
        $tahun = $this->request->getGet('tahun') ?? null;

        if ($tahun == null) {
            $kas_all = $this->model->get()->getResultArray();
        }else{
            $kas_all = $this->model->where('tahun', $tahun)->get()->getResultArray();
        }

        $kas_data = [];
        foreach ($kas_all as $kas) {
            $pemasukan = $this->pemasukanModel->where('id_kas', $kas['id_kas'])->selectSum('jumlah')->get()->getRowArray()['jumlah'];
            $pengeluaran = $this->pengeluaranModel->where('id_kas', $kas['id_kas'])->selectSum('jumlah')->get()->getRowArray()['jumlah'];

            array_push($kas_data, [
                "id_kas" => $kas['id_kas'],
                "bulan" => $kas['bulan'],
                "tahun" => $kas['tahun'],
                "publish" => $kas['publish'],
                "pemasukan" => $pemasukan,
                "pengeluaran" => $pengeluaran,
            ]);
        }

        $data = [
            'status' => 200,
            'message' => 'success',
            'data' => $kas_data,
            'aksiBy' => $aksiBy['nama'] ." (". $aksiBy['jabatan'] . ")",
        ];

        return $this->respond($data, 200);
    }

    public function lastData()
    {
        $kas = $this->model->where('publish', 0)->get()->getRowArray();
        $pemasukan = $this->pemasukanModel->where('id_kas', $kas['id_kas'])->selectSum('jumlah')->get()->getRowArray()['jumlah'];
        $pengeluaran = $this->pengeluaranModel->where('id_kas', $kas['id_kas'])->selectSum('jumlah')->get()->getRowArray()['jumlah'];
        
        $kas["pemasukan"] = $pemasukan;
        $kas["pengeluaran"] = $pengeluaran;

        $data = [
            'status' => 200,
            'message' => 'success',
            'data' => $kas,
        ];

        return $this->respond($data, 200);
    }
    
    public function publishData()
    {
        $tahun = $this->request->getGet('tahun') ?? null;
        
        $data = [
            'status' => 200,
            'message' => 'success',
            'data' => $this->model->getAllPublish($tahun),
        ];

        return $this->respond($data, 200);
    }

    public function publish()
    {
        $id_kas = $this->request->getVar('id_kas') ?? null;
        if($id_kas == null){
            $data = [
                'status' => 404,
                'error' => true,
                'message' => 'ID Kas tidak ditemukan'
            ];

            return $this->respond($data, 404);
        }else{
            $this->model->update($id_kas, ["publish" => 1]);
            
            $current_publish = $this->model->find($id_kas);
            $bulans = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $current_month = array_search($current_publish['bulan'], $bulans);
            $next_month = $current_month == 11 ? $bulans[0] : $bulans[$current_month+1];
            $next_kas = [
                "bulan" => $next_month,
                "tahun" => $current_publish['tahun']+1,
            ];
            
            $this->model->insert($next_kas);

            $data = [
                'status' => 200,
                'error' => false,
                'message' => 'Berhasil publish Kas'
            ];

            return $this->respond($data, 200);
        }
    }

    public function pemasukan()
    {
        if (!$this->validate([
            'id_kas'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'ID Kas harus diisi'
                ]
            ],
            'jumlah'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jumlah harus diisi'
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

        $data = [
            'id_kas' => $this->request->getVar('id_kas'),
            'jumlah' => $this->request->getVar('jumlah'),
            'keterangan' => $this->request->getVar('keterangan') ?? null,
            'tgl' => date('Y-m-d')
        ];

        $this->pemasukanModel->insert($data);

        $response = [
            'status' => 201,
            'error' => false,
            'message' => 'Data pemasukan berhasil ditambahkan'
        ];
        return $this->respondCreated($response, 201);
    }

    public function pengeluaran()
    {
        if (!$this->validate([
            'id_kas'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'ID Kas harus diisi'
                ]
            ],
            'jumlah'    => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jumlah harus diisi'
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
            $foto->move('uploads/pengeluaran_kas/', $namaFoto);
        }

        $data = [
            'id_kas' => $this->request->getVar('id_kas'),
            'jumlah' => $this->request->getVar('jumlah'),
            'keterangan' => $this->request->getVar('keterangan') ?? null,
            'foto' => $namaFoto,
            'tgl' => date('Y-m-d')
        ];

        $this->pengeluaranModel->insert($data);

        $response = [
            'status' => 201,
            'error' => false,
            'message' => 'Data pengeluaran berhasil ditambahkan'
        ];
        return $this->respondCreated($response, 201);
    }

    public function pemasukanData()
    {
        $id_kas = $this->request->getGet('id_kas') ?? null;

        if($id_kas == null){
            return json_encode([
                'status' => 404,
                'message' => 'success',
                'data' => "ID Kas tidak ditemukan",
            ]);
        }

        $data = [
            'status' => 200,
            'message' => 'success',
            'data' => $this->pemasukanModel->where('id_kas', $id_kas)->get()->getResultArray(),
        ];

        return $this->respond($data, 200);
    }

    public function pengeluaranData()
    {
        $id_kas = $this->request->getGet('id_kas') ?? null;

        if($id_kas == null){
            return json_encode([
                'status' => 404,
                'message' => 'success',
                'data' => "ID Kas tidak ditemukan",
            ]);
        }

        $data = [
            'status' => 200,
            'message' => 'success',
            'data' => $this->pengeluaranModel->where('id_kas', $id_kas)->get()->getResultArray(),
        ];

        return $this->respond($data, 200);
    }
}
