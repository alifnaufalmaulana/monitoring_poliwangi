<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GedungModel;
use CodeIgniter\HTTP\ResponseInterface;

class GedungController extends BaseController
{
    public function index(): string
    {
        $model = new GedungModel();

        // Ambil semua data gedung dari tabel 'gedung'
        $dataGedung = $model
            ->select('gedung.*')
            ->findAll();

        $data = [
            'judul' => 'Data Bangunan Poliwangi',
            'gedung' => $dataGedung,
        ];

        return view('gedung', $data);
    }
}
