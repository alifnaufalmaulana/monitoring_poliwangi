<?php

namespace App\Controllers\API;

use App\Models\GedungModel;
use App\Models\LantaiModel;
use App\Models\PerangkatModel;
use App\Models\RiwayatModel;
use CodeIgniter\RESTful\ResourceController;

class ApiController extends ResourceController
{
    protected $gedungModel;
    protected $lantaiModel;
    protected $perangkatModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->gedungModel = new GedungModel();
        $this->lantaiModel = new LantaiModel();
        $this->perangkatModel = new PerangkatModel();
        $this->riwayatModel = new RiwayatModel();
    }

    // API untuk semua gedung
    public function getGedung()
    {
        $data = $this->gedungModel->findAll();
        return $this->respond($data);
    }

    // API untuk lantai berdasarkan gedung_id
    public function getLantaiByGedung($id_gedung)
    {
        $data = $this->lantaiModel->where('id_gedung', $id_gedung)->findAll();
        return $this->respond($data);
    }

    public function getPerangkatByLantai($id_lantai)
    {
        $db = \Config\Database::connect();

        // Ambil data perangkat terakhir (status terbaru) berdasarkan id_perangkat
        $subquery = $db->table('riwayat_perangkat')
            ->select('MAX(id_riwayat) as max_id')
            ->join('perangkat', 'perangkat.id_perangkat = riwayat_perangkat.id_perangkat')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan')
            ->where('ruangan.id_lantai', $id_lantai)
            ->groupBy('riwayat_perangkat.id_perangkat');

        $builder = $db->table('riwayat_perangkat');
        $builder->select('perangkat.id_perangkat, perangkat.nama_perangkat, perangkat.pos_x, perangkat.pos_y, riwayat_perangkat.status_perangkat');
        $builder->join('perangkat', 'perangkat.id_perangkat = riwayat_perangkat.id_perangkat');
        $builder->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan');
        $builder->where('ruangan.id_lantai', $id_lantai);
        $builder->whereIn('riwayat_perangkat.id_riwayat', $subquery);

        $result = $builder->get()->getResultArray();

        return $this->response->setJSON($result);
    }
}
