<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table            = 'riwayat_perangkat';
    protected $primaryKey       = 'id_riwayat';
    protected $allowedFields    = ['id_perangkat', 'status_perangkat', 'aksi', 'waktu',];
    protected $useTimestamps = false;


    public function getFilteredRiwayat($filters = [])
    {
        // Subquery kebencanaan untuk ambil satu data per perangkat & waktu
        $subKebencanaan = $this->db->table('kebencanaan')
            ->select('MIN(id_kebencanaan) as id_kebencanaan, id_perangkat, waktu')
            ->groupBy(['id_perangkat', 'waktu']);

        $builder = $this->select('
            riwayat_perangkat.*, 
            perangkat.nama_perangkat, 
            ruangan.nama_ruangan, 
            lantai.nama_lantai, 
            gedung.nama_gedung, 
            kebencanaan.jenis_bencana
        ')
            ->join('perangkat', 'perangkat.id_perangkat = riwayat_perangkat.id_perangkat')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan')
            ->join('lantai', 'lantai.id_lantai = ruangan.id_lantai')
            ->join('gedung', 'gedung.id_gedung = lantai.id_gedung')
            ->join('(' . $subKebencanaan->getCompiledSelect() . ') AS kbnc', 'kbnc.id_perangkat = riwayat_perangkat.id_perangkat AND kbnc.waktu = riwayat_perangkat.waktu', 'left')
            ->join('kebencanaan', 'kebencanaan.id_kebencanaan = kbnc.id_kebencanaan', 'left')
            ->orderBy('riwayat_perangkat.waktu', 'DESC');

        // Filter
        if (!empty($filters['tanggal_awal']) && !empty($filters['tanggal_akhir'])) {
            $builder->where("DATE(riwayat_perangkat.waktu) >=", $filters['tanggal_awal'])
                ->where("DATE(riwayat_perangkat.waktu) <=", $filters['tanggal_akhir']);
        }

        if (!empty($filters['status_perangkat'])) {
            $builder->where('riwayat_perangkat.status_perangkat', $filters['status_perangkat']);
        }

        if (!empty($filters['aksi'])) {
            $builder->where('riwayat_perangkat.aksi', $filters['aksi']);
        }

        if (!empty($filters['id_gedung'])) {
            $builder->where('gedung.id_gedung', $filters['id_gedung']);
        }

        if (!empty($filters['id_lantai'])) {
            $builder->where('lantai.id_lantai', $filters['id_lantai']);
        }

        if (!empty($filters['id_ruangan'])) {
            $builder->where('ruangan.id_ruangan', $filters['id_ruangan']);
        }

        if (!empty($filters['jenis_bencana'])) {
            $builder->where('riwayat_perangkat.status_perangkat', 'bahaya');
            $builder->where('kebencanaan.jenis_bencana', $filters['jenis_bencana']);
        }

        return $builder->findAll();
    }
}
