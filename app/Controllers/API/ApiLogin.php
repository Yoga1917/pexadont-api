<?php

namespace App\Controllers\API;

use App\Models\PengurusModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ApiLogin extends ResourceController
{
    protected $modelName = 'App\Models\WargaModel';
    protected $format    = 'json';
    protected $pengurusModel;

    public function __construct()
    {
        $this->pengurusModel = new PengurusModel();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function login()
    {
        $nik = $this->request->getVar('nik');
        $password = $this->request->getVar('password');

        $data = $this->model->find($nik);
        if($data){
            $verify_pass = password_verify($password, $data['password']);

            if($verify_pass){
                $response = [
                    'status' => 200,
                    'error' => false,
                    'data' => $data
                ];
                return $this->respond($response, 200);
            }else{
                $response = [
                    'status' => 401,
                    'error' => true,
                    'data' => 'Informasi login tidak cocok!'
                ];
                return $this->respond($response, 401);
            }
        }else{
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Warga tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }

    public function loginPengurus()
    {
        $nik = $this->request->getVar('nik');
        $password = $this->request->getVar('password');

        $data = $this->pengurusModel->relasiWargaBynik($nik);
        if($data){
            $verify_pass = password_verify($password, $data[0]['password']);

            if($verify_pass){
                $response = [
                    'status' => 200,
                    'error' => false,
                    'data' => $data[0]
                ];
                return $this->respond($response, 200);
            }else{
                $response = [
                    'status' => 401,
                    'error' => true,
                    'data' => 'Informasi login tidak cocok!'
                ];
                return $this->respond($response, 401);
            }
        }else{
            $response = [
                'status' => 404,
                'error' => true,
                'data' => 'Data pengurus tidak ditemukan'
            ];
            return $this->respond($response, 404);
        }
    }
}