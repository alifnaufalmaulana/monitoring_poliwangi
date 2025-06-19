<?php

namespace App\Controllers\API;

use App\Models\GedungModel;
use App\Models\LantaiModel;
use App\Models\RuanganModel;
use App\Models\PerangkatModel;
use App\Models\RiwayatModel;
use CodeIgniter\RESTful\ResourceController;

class ApiController extends ResourceController
{
    protected $gedungModel;
    protected $lantaiModel;
    protected $ruanganModel;
    protected $perangkatModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->gedungModel = new GedungModel();
        $this->lantaiModel = new LantaiModel();
        $this->ruanganModel = new RuanganModel();
        $this->perangkatModel = new PerangkatModel();
        $this->riwayatModel = new RiwayatModel();
    }

    // ✅ API untuk semua gedung
    public function getGedung()
    {
        $data = $this->gedungModel->findAll();
        return $this->respond($data);
    }

    // ✅ API untuk lantai berdasarkan id_gedung
    public function getLantaiByGedung($id_gedung)
    {
        $data = $this->lantaiModel->where('id_gedung', $id_gedung)->findAll();
        return $this->respond($data);
    }

    // ✅ API untuk ruangan berdasarkan id_lantai (perbaikan dilakukan di sini)
    public function getRuanganByLantai($id_lantai)
    {
        $data = $this->ruanganModel->where('id_lantai', $id_lantai)->findAll();
        return $this->respond($data);
    }

    // ✅ API untuk perangkat berdasarkan lantai (status terakhir)
    public function getPerangkatByLantai($id_lantai)
    {
        // Jika tidak login, kembalikan array kosong (publik tidak bisa lihat marker)
        if (!session()->has('logged_in')) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();

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

    // Method baru untuk mendapatkan detail lokasi berdasarkan ID perangkat
    public function getPerangkatDetails($id_perangkat)
    {
        $perangkatDetail = $this->perangkatModel->getLokasiPerangkat($id_perangkat);
        if ($perangkatDetail) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $perangkatDetail
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Detail perangkat tidak ditemukan.'
            ], 404);
        }
    }
}
