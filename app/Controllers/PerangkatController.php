<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerangkatModel;

class PerangkatController extends BaseController
{
    public function index()
    {
        $model = new PerangkatModel();
        $dataPerangkat = $model
            ->select('perangkat.*, ruangan.nama_ruangan, lantai.nama_lantai, gedung.nama_gedung')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->join('lantai', 'ruangan.id_lantai = lantai.id_lantai')
            ->join('gedung', 'ruangan.id_gedung = gedung.id_gedung')
            ->findAll();

        $data = [
            'judul' => 'Data Perangkat',
            'perangkat' => $dataPerangkat,
        ];
        
        return view('perangkat', $data);
    }
}
