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
        $dataGedung = $model
            ->select('gedung.*')
            ->findAll();

        $data = [
            'judul' => 'Data Gedung',
            'page' => 'gedung',
            'gedung' => $dataGedung,
        ];

        return view('template', $data);
    }
}
