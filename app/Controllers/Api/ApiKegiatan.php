<?php

namespace App\Controllers\API;

use App\Models\KegiatanModel;
use App\Models\PengurusModel;
use App\Models\WargaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiKegiatan extends ResourceController
{
    protected $KegiatanModel;
    protected $pengurusModel;
    protected $WargaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->KegiatanModel    = new KegiatanModel();
        $this->WargaModel       = new WargaModel();
        $this->pengurusModel = new PengurusModel();
    }
    
    public function index()
    {
        $aksiBy = $this->pengurusModel->getByJabatan("Sekretaris");

        $data = [
            'status'    => 200,
            'error'     => false,
            'data'      => $this->KegiatanModel->relasiWarga(),
            'aksiBy' => $aksiBy['nama'] ." (". $aksiBy['jabatan'] . ")",
            'fotoAksiBy' => $aksiBy['foto']
        ];

        return $this->respond($data, 200);
    }

    public function create()
    {
        if (!$this->validate([
            'nik' => [
                'rules' => 'required',
                'errors' => ['required' => 'NIK Warga Harus Diisi']
            ],
            'nama_kegiatan' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Nama Kegiatan Harus Diisi',
                ]
            ],
            'keterangan' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Keterangan Harus Diisi',
                ]
            ],
            'tgl' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'Tanggal Harus Diisi',
                ]
            ],
            'proposal' => [
                'rules'     => 'uploaded[proposal]|ext_in[proposal,pdf]',
                'errors'    => [
                    'uploaded'      => 'File Proposal Harus Diisi',
                    'ext_in'        => 'File Proposal Harus Berformat PDF'
                ]
            ],
        ])) {
            $data = [
                'status'    => 400,
                'error'     => true,
                'message'   => $this->validator->getErrors()
            ];

            return $this->respond($data, 400);
        }

        $proposal = $this->request->getFile('proposal');
        $namaProposal = $proposal->getRandomName();
        $proposal->move('uploads/kegiatan/proposal/', $namaProposal);

        $data = [
            'nik'             => $this->request->getPost('nik'),
            'nama_kegiatan'   => $this->request->getPost('nama_kegiatan'),
            'keterangan'      => $this->request->getPost('keterangan'),
            'tgl'             => $this->request->getPost('tgl'),
            'proposal'        => $namaProposal,
        ];

        $this->KegiatanModel->insert($data);
        $response = [
            'status'    => 201,
            'error'     => false,
            'message'   => 'Data Kegiatan Berhasil Disimpan.'
        ];

        return $this->respondCreated($response, 201);
    }

    public function lpj()
    {
        if (!$this->validate([
            'id_kegiatan' => [
                'rules'     => 'required',
                'errors'    => ['required' => 'Tidak ditemukan ID Kegiatan'],
            ],
            'lpj' => [
                'rules'     => 'uploaded[lpj]|ext_in[lpj,pdf]',
                'errors'    => [
                    'uploaded'      => 'File LPJ Harus Diisi',
                    'ext_in'        => 'File LPJ Harus Berformat PDF'
                ]
            ],
        ])) {
            $data = [
                'status'    => 400,
                'error'     => true,
                'message'   => $this->validator->getErrors()
            ];

            return $this->respond($data, 400);
        }

        $lpj = $this->request->getFile('lpj');
        $namaLpj = $lpj->getRandomName();
        $lpj->move('uploads/kegiatan/lpj/', $namaLpj);
        
        $this->KegiatanModel->update($this->request->getPost('id_kegiatan'), [
            'lpj' => $namaLpj
        ]);

        $response = [
            'status'    => 200,
            'error'     => false,
            'message'   => 'Data LPJ Berhasil Diupload.'
        ];

        return $this->respond($response, 200);
    }
}
