<?php

namespace App\Models;

use CodeIgniter\Model;

class PengurusModel extends Model
{
    protected $table            = 'pengurus';
    protected $primaryKey       = 'id_pengurus';
    protected $allowedFields    = ['nik', 'jabatan', 'periode', 'status_pengurus']; // ini yang ditambahkan

    public function relasiWarga($periode)
    {
        return $this->db->table('pengurus')
            ->join('warga', 'warga.nik = pengurus.nik')
            ->where('pengurus.periode', $periode)
            ->get()->getResultArray();
    }

    public function relasiWargaBynik($nik)
    {
        return $this->db->table('pengurus')
            ->join('warga', 'warga.nik = pengurus.nik')
            ->where('pengurus.nik', $nik)
            ->get()->getRowArray();
    }

    public function getByJabatan($jabatan)
    {
        return $this->db->table('pengurus')
            ->join('warga', 'warga.nik = pengurus.nik')
            ->where('pengurus.jabatan', $jabatan)
            ->orderBy('pengurus.id_pengurus', 'desc')
            ->get()->getRowArray();
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
