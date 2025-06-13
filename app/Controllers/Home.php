<?php

namespace App\Controllers;

use App\Models\GedungModel;

class Home extends BaseController
{
    public function index()
    {
        $gedungModel = new GedungModel();
        $data = [
            'judul' => 'Dashboard',
            'gedung' => $gedungModel->findAll()
        ];

        return view('dashboard', $data);
    }
}
