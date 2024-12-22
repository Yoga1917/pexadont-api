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

    public function passwordReset(){
        // generate random password baru
        helper('text');
        $newPassword = random_string('alnum', 6);
        // update warga password
        $nik = $this->request->getVar('nik');
        $this->model->update($nik, [
            "password" => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        // send whatsapp notif
        $data = $this->model->find($nik);
        $text = "Halo " .$data['nama']. ",\nAnda telah melakukan tindakan reset password pada aplikasi pexadont, berikut informasi password baru anda:\n\n";
        $text = $text . "NIK : " . $data['nik'];
        $text = $text . "\nPassword : " . $newPassword;
        $this->sendNotif(
            $data['no_wa'],
            $text
        );

        // api response
        $response = [
            'status' => 200,
            'error' => false,
            'data' => "Password baru berhasil dikirim ke nomor whatsapp"
        ];
        return $this->respond($response, 200);
    }

    public function passwordUbah(){
        $dataPassword = $this->model->find($this->request->getVar('nik'))['password'];
        $passwordOld = $this->request->getVar('password_old');
        $passwordNew = $this->request->getVar('password_new');

        $verify_pass = password_verify($passwordOld, $dataPassword);
        if(!$verify_pass){
            $response = [
                'status' => 401,
                'error' => true,
                'data' => 'Password lama yang di inputkan tidak sesuai'
            ];
            return $this->respond($response, 401);
        }else{
            $this->model->update($this->request->getVar('nik'), [
                "password" => password_hash($passwordNew, PASSWORD_DEFAULT),
            ]);

            $response = [
                'status' => 200,
                'error' => false,
                'data' => 'Password berhasil diperbarui'
            ];
            return $this->respond($response, 200);
        }
    }

    // helper func send whatsapp
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