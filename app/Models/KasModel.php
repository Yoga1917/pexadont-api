<?php

namespace App\Models;

use CodeIgniter\Model;

class KasModel extends Model
{
    protected $table            = 'kas';
    protected $primaryKey       = 'id_kas';
    protected $allowedFields    = ['bulan', 'tahun', 'publish'];

    public function getAll($tahun)
    {
        if($tahun == null){
            $data = $this->db->table('kas')
            ->select("kas.id_kas, kas.bulan, kas.tahun, kas.publish, SUM(pemasukan.jumlah) as pemasukan, SUM(pengeluaran.jumlah) as pengeluaran")
            ->join('pemasukan', 'pemasukan.id_kas = kas.id_kas')
            ->join('pengeluaran', 'pengeluaran.id_kas = kas.id_kas')
            ->groupBy('kas.id_kas')
            ->get()->getResultArray();
        }else{
            $data = $this->db->table('kas')
            ->select("kas.id_kas, kas.bulan, kas.tahun, kas.publish, SUM(pemasukan.jumlah) as pemasukan, SUM(pengeluaran.jumlah) as pengeluaran")
            ->join('pemasukan', 'pemasukan.id_kas = kas.id_kas')
            ->join('pengeluaran', 'pengeluaran.id_kas = kas.id_kas')
            ->groupBy('kas.id_kas')
            ->where('kas.tahun', $tahun)
            ->get()->getResultArray();
        }
        
        return $data;
    }

    public function getAllPublish($tahun)
    {
        if($tahun == null){
            $data = $this->db->table('kas')
            ->select("kas.id_kas, kas.bulan, kas.tahun, kas.publish, SUM(pemasukan.jumlah) as pemasukan, SUM(pengeluaran.jumlah) as pengeluaran")
            ->join('pemasukan', 'pemasukan.id_kas = kas.id_kas')
            ->join('pengeluaran', 'pengeluaran.id_kas = kas.id_kas')
            ->groupBy('kas.id_kas')
            ->where('kas.publish', 1)
            ->get()->getResultArray();
        }else{
            $data = $this->db->table('kas')
            ->select("kas.id_kas, kas.bulan, kas.tahun, kas.publish, SUM(pemasukan.jumlah) as pemasukan, SUM(pengeluaran.jumlah) as pengeluaran")
            ->join('pemasukan', 'pemasukan.id_kas = kas.id_kas')
            ->join('pengeluaran', 'pengeluaran.id_kas = kas.id_kas')
            ->groupBy('kas.id_kas')
            ->where('kas.publish', 1)
            ->where('kas.tahun', $tahun)
            ->get()->getResultArray();
        }

        return $data;
    }

    public function getLastData()
    {
        return $this->db->table('kas')
            ->select("kas.id_kas, kas.bulan, kas.tahun, kas.publish, SUM(pemasukan.jumlah) as pemasukan, SUM(pengeluaran.jumlah) as pengeluaran")
            ->join('pemasukan', 'pemasukan.id_kas = kas.id_kas')
            ->join('pengeluaran', 'pengeluaran.id_kas = kas.id_kas')
            ->groupBy('kas.id_kas')
            ->orderBy('kas.id_kas')
            ->limit(1)
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
