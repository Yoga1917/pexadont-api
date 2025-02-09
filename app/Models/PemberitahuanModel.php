<?php

namespace App\Models;

use CodeIgniter\Model;

class PemberitahuanModel extends Model
{
    protected $table            = 'pemberitahuan';
    protected $primaryKey       = 'id_pemberitahuan';
    protected $allowedFields    = ['pemberitahuan', 'deskripsi', 'tgl', 'file', 'id_pengurus'];

    public function getPemberitahuanWithPengurus()
    {
        return $this->db->table('pemberitahuan')
            ->select('pemberitahuan.*, warga.nama as aksiBy, warga.foto as fotoAksiBy')
            ->join('pengurus', 'pengurus.id_pengurus = pemberitahuan.id_pengurus', 'left')
            ->join('warga', 'warga.nik = pengurus.nik', 'left') // Join ke tabel warga
            ->orderBy('tgl', 'desc')
            ->get()
            ->getResultArray();
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
