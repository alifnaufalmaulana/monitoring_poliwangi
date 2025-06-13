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
        $perangkatModel = new PerangkatModel();
        $perangkat = $perangkatModel
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan')
            ->join('riwayat_perangkat', 'riwayat_perangkat.id_perangkat = perangkat.id_perangkat')
            ->where('ruangan.id_lantai', $id_lantai)
            ->select('perangkat.id_perangkat, perangkat.nama_perangkat, perangkat.pos_x, perangkat.pos_y, riwayat_perangkat.status_perangkat')
            ->findAll();



        return $this->response->setJSON($perangkat);
    }
}
