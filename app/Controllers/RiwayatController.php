<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RiwayatModel;
use App\Models\GedungModel;
use App\Models\KebencanaanModel;
use App\Models\LantaiModel;
use App\Models\RuanganModel;
use CodeIgniter\HTTP\ResponseInterface;

class RiwayatController extends BaseController
{
    public function index()
    {
        // Ambil parameter filter dari URL (GET)
        $filters = $this->request->getGet();

        $riwayatModel = new RiwayatModel();
        $kebencanaanModel = new KebencanaanModel();

        $data = [
            'judul'        => 'Riwayat Perangkat',
            'riwayat'      => $riwayatModel->getFilteredRiwayat($filters),
            'gedung'       => (new GedungModel())->findAll(),
            'lantai'       => (new LantaiModel())->findAll(),
            'ruangan'      => (new RuanganModel())->findAll(),
            'kebencanaan'  => $kebencanaanModel->getJenisBencanaUnik(),
            'filters'      => $filters,
        ];

        return view('riwayat', $data);
    }
}
