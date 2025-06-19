<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KebencanaanModel;
use CodeIgniter\HTTP\ResponseInterface;

class KebencanaanController extends BaseController
{
    public function index()
    {
        $kebencanaanModel = new KebencanaanModel();

        // ambil daftar unik jenis_bencana
        $jenisBencana = $kebencanaanModel
            ->select('jenis_bencana')
            ->distinct()
            ->findAll();

        $data['bencana'] = $jenisBencana;

        return view('riwayat', $data);
    }
}
