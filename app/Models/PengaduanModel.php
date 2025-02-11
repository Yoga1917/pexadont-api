<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaduanModel extends Model
{
    protected $table            = 'pengaduan';
    protected $primaryKey       = 'id_pengaduan';
    protected $allowedFields    = ['id_pengaduan', 'nik', 'isi', 'foto', 'tgl', 'jenis', 'balasan', 'id_pengurus'];

    public function relasiWarga()
    {
        return $this->db->table('pengaduan')
            ->join('warga', 'warga.nik = pengaduan.nik')
            ->select('id_pengaduan, pengaduan.nik, pengaduan.isi, pengaduan.foto, pengaduan.tgl, pengaduan.jenis, pengaduan.balasan, warga.nama, warga.foto as foto_warga') // ini yang ditambahkan
            ->get()->getResultArray();
    }

    public function findByJenis($jenis)
    {
        return $this->db->table('pengaduan')
            ->join('warga', 'warga.nik = pengaduan.nik')
            ->select('id_pengaduan, pengaduan.nik, pengaduan.isi, pengaduan.foto, pengaduan.tgl, pengaduan.jenis, pengaduan.balasan, warga.nama, warga.foto as foto_warga') // ini yang ditambahkan
            ->where('pengaduan.jenis', $jenis)
            ->get()->getResultArray();
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
