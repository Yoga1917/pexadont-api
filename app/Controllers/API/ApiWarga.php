<?php

namespace App\Controllers\API;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiWarga extends ResourceController
{
    protected $modelName = 'App\Models\WargaModel';
    protected $format    = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = [
            'status' => 200,
            'error' => false,
            'data' => $this->model->findAll()
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
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
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

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
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

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->model->delete($id);
            unlink('uploads/warga/' . $data['foto']);
            $response = [
                'status' => 203,
                'error' => false,
                'data' => 'Warga berhasil dihapus'
            ];
            return $this->respondDeleted($response, 203);
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Warga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }

    public function simpanToken()
    {
        $token = $this->request->getVar('token'); // Token FCM yang diterima dari aplikasi

        // Simpan token ke database
        $wargaModel = new \App\Models\WargaModel();
        $wargaModel->update($this->request->getVar('id'), ['fcm_token' => $token]);

        return $this->response->setJSON(['status' => 'Token saved']);
    }
}
