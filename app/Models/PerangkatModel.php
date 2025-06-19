<?php

namespace App\Models;

use CodeIgniter\Model;

class PerangkatModel extends Model
{
    protected $table            = 'perangkat';
    protected $primaryKey       = 'id_perangkat';
    // protected $useAutoIncrement = true;
    // protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    protected $allowedFields    = ['id_perangkat', 'nama_perangkat', 'id_ruangan', 'jenis_perangkat', 'latitude', 'longitude', 'waktu', 'pos_x', 'pos_y',];

    public function getFilterPerangkat($gedungId = null, $lantaiId = null, $ruanganId = null, $status = null,)
    {
        $subStatus = $this->db->table('riwayat_perangkat r1')
            ->select('r1.id_perangkat, r1.status_perangkat')
            ->join(
                '(SELECT id_perangkat, MAX(waktu) AS max_waktu FROM riwayat_perangkat GROUP BY id_perangkat) r2',
                'r1.id_perangkat = r2.id_perangkat AND r1.waktu = r2.max_waktu'
            );

        $this->select('
        perangkat.*,
        ruangan.nama_ruangan,
        lantai.nama_lantai,
        gedung.nama_gedung,
        rs.status_perangkat
    ')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan', 'left')
            ->join('lantai', 'lantai.id_lantai = ruangan.id_lantai', 'left')
            ->join('gedung', 'gedung.id_gedung = ruangan.id_gedung', 'left')
            ->join('(' . $subStatus->getCompiledSelect() . ') AS rs', 'rs.id_perangkat = perangkat.id_perangkat', 'left');

        if ($gedungId) {
            $this->where('gedung.id_gedung', $gedungId);
        }

        if ($lantaiId) {
            $this->where('lantai.id_lantai', $lantaiId);
        }

        if ($ruanganId) {
            $this->where('ruangan.id_ruangan', $ruanganId);
        }

        if ($status) {
            $this->where('rs.status_perangkat', $status);
        }

        return $this->findAll();
    }


    // Method untuk mendapatkan detail perangkat beserta lokasi (ruangan, lantai, gedung)
    public function getLokasiPerangkat($id_perangkat)
    {
        return $this->select('perangkat.id_perangkat, perangkat.nama_perangkat,
                              ruangan.id_ruangan, ruangan.nama_ruangan,
                              lantai.id_lantai, lantai.nama_lantai,
                              gedung.id_gedung, gedung.nama_gedung')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan', 'left')
            ->join('lantai', 'lantai.id_lantai = ruangan.id_lantai', 'left') // Tetap join ke lantai
            ->join('gedung', 'gedung.id_gedung = ruangan.id_gedung', 'left') // <-- PERUBAHAN PENTING DI SINI
            ->where('perangkat.id_perangkat', $id_perangkat)
            ->first();
    }



    // protected bool $allowEmptyInserts = false;
    // protected bool $updateOnlyChanged = true;

    // protected array $casts = [];
    // protected array $castHandlers = [];

    // // Dates
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $dateFormat    = 'datetime';
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
