<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanModel extends Model
{
    protected $table            = 'kegiatan';
    protected $primaryKey       = 'id_kegiatan';
    protected $allowedFields    = ['nik', 'nama_kegiatan', 'keterangan', 'tgl', 'proposal', 'lpj', 'id_pengurus'];

    public function relasiWarga()
    {
        return $this->db->table('kegiatan')
            ->join('warga', 'warga.nik = kegiatan.nik')
            ->select('kegiatan.*, warga.nama as ketua_pelaksana, warga.foto as foto_ketua_pelaksana') // ini yang ditambahkan
            ->orderBy('kegiatan.tgl', 'desc')
            ->get()->getResultArray();
    }

    public function getKegiatanWithPengurus()
    {
        return $this->db->table('kegiatan')
            ->select('kegiatan.*, warga.nama as aksiBy, warga.foto as fotoAksiBy')
            ->join('pengurus', 'pengurus.id_pengurus = kegiatan.id_pengurus', 'left')
            ->join('warga', 'warga.nik = pengurus.nik', 'left') // Join ke tabel warga
            ->get()
            ->getResultArray();
    }

    public function getKegiatanLengkap()
    {
        $kegiatanRelasiWarga = $this->relasiWarga();
        $kegiatanWithPengurus = $this->getKegiatanWithPengurus();

        // Gabungkan dua hasil query berdasarkan ID Kegiatan
        foreach ($kegiatanRelasiWarga as &$kegiatan) {
            foreach ($kegiatanWithPengurus as $pengurus) {
                if ($kegiatan['id_kegiatan'] == $pengurus['id_kegiatan']) {
                    $kegiatan['aksiBy'] = $pengurus['aksiBy'];
                    $kegiatan['fotoAksiBy'] = $pengurus['fotoAksiBy'];
                }
            }
        }
        return $kegiatanRelasiWarga;
    }

    // protected $useAutoIncrement = true;
    // protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;

    // protected bool $allowEmptyInserts = false;
    // protected bool $updateOnlyChanged = true;

    // protected array $casts = [];
    // protected array $castHandlers = [];

    // // Dates
    // protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];
}
