<?php

namespace App\Models;

use CodeIgniter\Model;

class PerangkatModel extends Model
{
    protected $table            = 'perangkat';
    protected $primaryKey       = 'id_perangkat';
    protected $allowedFields    = [
        'id_perangkat',
        'nama_perangkat',
        'id_ruangan',
        'jenis_perangkat',
        'latitude',
        'longitude',
        'waktu',
        'pos_x',
        'pos_y',
    ];

    // Ambil daftar perangkat dengan filter gedung, lantai, ruangan, dan status
    public function getFilterPerangkat($gedungId = null, $lantaiId = null, $ruanganId = null, $status = null)
    {
        // Subquery untuk ambil status terbaru dari setiap perangkat
        $subStatus = $this->db->table('riwayat_perangkat r1')
            ->select('r1.id_perangkat, r1.status_perangkat')
            ->join(
                '(SELECT id_perangkat, MAX(waktu) AS max_waktu FROM riwayat_perangkat GROUP BY id_perangkat) r2',
                'r1.id_perangkat = r2.id_perangkat AND r1.waktu = r2.max_waktu'
            );

        // Join ke tabel ruangan, lantai, gedung, dan status terakhir perangkat
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

        // Tambahkan filter sesuai parameter
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

    // Ambil detail lokasi perangkat: nama ruangan, lantai, dan gedung-nya
    public function getLokasiPerangkat($id_perangkat)
    {
        return $this->select('
                perangkat.id_perangkat, perangkat.nama_perangkat,
                ruangan.id_ruangan, ruangan.nama_ruangan,
                lantai.id_lantai, lantai.nama_lantai,
                gedung.id_gedung, gedung.nama_gedung
            ')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan', 'left')
            ->join('lantai', 'lantai.id_lantai = ruangan.id_lantai', 'left')
            ->join('gedung', 'gedung.id_gedung = ruangan.id_gedung', 'left')
            ->where('perangkat.id_perangkat', $id_perangkat)
            ->first();
    }
}
