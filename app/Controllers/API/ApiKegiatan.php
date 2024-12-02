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
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'status'    => 200,
            'error'     => false,
            'message'   => 'Data Kegiatan Berhasil Diambil',
            'data'      => $this->KegiatanModel->relasiWarga()
        ];

        return $this->respond($data, 200);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        $data = [
            'status'    => 200,
            'error'     => false,
            'message'   => 'Data Warga Berhasil Diambil',
            'data'      => $this->WargaModel->findAll()
        ];

        return $this->respond($data, 200);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (!$this->validate([
            'nik'       => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'NIK Warga Harus Diisi',
                ]
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
                'error'     => $this->validator->getErrors(),
                'message'   => 'Data yang dikirimkan tidak lengkap.'
            ];

            return $this->respond($data, 400);
        }

        $nik   = $this->WargaModel->find($this->request->getPost('nik'));
        if (empty($nik)) {
            $data = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data Warga Tidak Ditemukan'
            ];

            return $this->respond($data, 404);
        }

        $proposal = $this->request->getFile('proposal');
        $proposal->move('uploads/proposal/' . $proposal->getName());

        $data = [
            'nik'           => $this->request->getPost('nik'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'tgl'           => $this->request->getPost('tgl'),
            'proposal'      => $proposal->getName(),
            'lpj'           => ''
        ];

        $this->KegiatanModel->insert($data);
        $response = [
            'status'    => 201,
            'error'     => null,
            'message'   => 'Data Kegiatan Berhasil Disimpan.'
        ];

        return $this->respondCreated($response, 201);
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        $data = $this->KegiatanModel->getKegiatan($id);

        if ($data) {
            $response = [
                'status'    => 200,
                'error'     => false,
                'message'   => 'Data Kegiatan Berhasil Diambil',
                'data'      => $data,
                'warga'     => $this->WargaModel->findAll()
            ];

            return $this->respond($response, 200);
        } else {
            $response = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data Kegiatan Tidak Ditemukan'
            ];

            return $this->respond($response, 404);
        }
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        if (!$this->validate([
            'nik'       => [
                'rules'     => 'required',
                'errors'    => [
                    'required'      => 'NIK Warga Harus Diisi',
                ]
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
                'rules'     => 'ext_in[proposal,pdf]',
                'errors'    => [
                    'uploaded'      => 'File Proposal Harus Diisi',
                    'ext_in'        => 'File Proposal Harus Berformat PDF'
                ]
            ],
        ])) {
            $data = [
                'status'    => 400,
                'error'     => $this->validator->getErrors(),
                'message'   => 'Data yang dikirimkan tidak lengkap.'
            ];

            return $this->respond($data, 400);
        }

        $nik   = $this->WargaModel->find($this->request->getPost('nik'));
        if (empty($nik)) {
            $data = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data Warga Tidak Ditemukan'
            ];

            return $this->respond($data, 404);
        }

        $proposal = $this->request->getFile('proposal');
        if ($proposal != null) {
            $proposal->move('uploads/proposal/' . $proposal->getName());
            $data = [
                'nik'           => $this->request->getPost('nik'),
                'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
                'keterangan'    => $this->request->getPost('keterangan'),
                'tgl'           => $this->request->getPost('tgl'),
                'proposal'      => $proposal->getName(),
                'lpj'           => ''
            ];
        } else {
            $data = [
                'nik'           => $this->request->getPost('nik'),
                'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
                'keterangan'    => $this->request->getPost('keterangan'),
                'tgl'           => $this->request->getPost('tgl'),
                'lpj'           => ''
            ];
        }

        $this->KegiatanModel->update($id, $data);
        $response = [
            'status'    => 202,
            'error'     => null,
            'message'   => 'Data Kegiatan Berhasil Diupdate.'
        ];

        return $this->respond($response, 202);
    }

    public function UpdateLPJ($id)
    {
        if (!$this->validate([
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
                'error'     => $this->validator->getErrors(),
                'message'   => 'Data yang dikirimkan tidak lengkap.'
            ];

            return $this->respond($data, 400);
        }

        $lpj = $this->request->getFile('lpj');
        $lpj->move('uploads/lpj/' . $lpj->getName());
        $data = [
            'lpj'           => $lpj->getName()
        ];

        $this->KegiatanModel->update($id, $data);
        $response = [
            'status'    => 202,
            'error'     => null,
            'message'   => 'Data LPJ Berhasil Diupload.'
        ];

        return $this->respond($response, 202);
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $data = $this->KegiatanModel->find($id);

        if ($data) {
            $this->KegiatanModel->delete($id);
            if ($data['proposal'] != null) {
                unlink('uploads/proposal/' . $data['proposal']);
            }
            if ($data['lpj'] != null) {
                unlink('uploads/lpj/' . $data['lpj']);
            }
            $response = [
                'status'    => 203,
                'message'   => 'Data Kegiatan Berhasil Dihapus.'
            ];

            return $this->respond($response, 203);
        } else {
            $response = [
                'status'    => 404,
                'error'     => true,
                'message'   => 'Data Kegiatan Tidak Ditemukan'
            ];

            return $this->respond($response, 404);
        }
    }
}
