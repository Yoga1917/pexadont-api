<?php

namespace App\Controllers\API;

use App\Models\KegiatanModel;
use App\Models\WargaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiKegiatan extends ResourceController
{
    protected $KegiatanModel;
    protected $WargaModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->KegiatanModel    = new KegiatanModel();
        $this->WargaModel       = new WargaModel();
    }
    
    public function index()
    {
        $kegiatan = $this->KegiatanModel->getKegiatanLengkap();

        $data = [
            'status'    => 200,
            'error'     => false,
            'data'      => $kegiatan
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
            'proposal' => [
                'rules'     => 'uploaded[proposal]|ext_in[proposal,pdf]',
                'errors'    => [
                    'uploaded'      => 'File Proposal Harus Diisi',
                    'ext_in'        => 'File Proposal Harus Berformat PDF'
                ]
            ],
            'id_pengurus'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'id_pengurus harus diisi.'
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

        $proposal = $this->request->getFile('proposal');
        $namaProposal = $proposal->getRandomName();
        $proposal->move('uploads/kegiatan/proposal/', $namaProposal);

        $data = [
            'nik'             => $this->request->getPost('nik'),
            'nama_kegiatan'   => $this->request->getPost('nama_kegiatan'),
            'keterangan'      => $this->request->getPost('keterangan'),
            'tgl'             => date('Y-m-d'),
            'proposal'        => $namaProposal,
            'id_pengurus'     => $this->request->getPost('id_pengurus'),
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
            'id_pengurus'  => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'id_pengurus harus diisi.'
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

        $lpj = $this->request->getFile('lpj');
        $namaLpj = $lpj->getRandomName();
        $lpj->move('uploads/kegiatan/lpj/', $namaLpj);
        
        $this->KegiatanModel->update($this->request->getPost('id_kegiatan'), [
            'lpj'             => $namaLpj,
            'id_pengurus'     => $this->request->getPost('id_pengurus'),
        ]);

        $response = [
            'status'    => 200,
            'error'     => false,
            'message'   => 'Data LPJ Berhasil Diupload.'
        ];

        return $this->respond($response, 200);
    }
}
