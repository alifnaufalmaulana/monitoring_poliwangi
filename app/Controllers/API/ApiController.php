<?php

namespace App\Controllers\API;

use App\Models\GedungModel;
use App\Models\LantaiModel;
use CodeIgniter\RESTful\ResourceController;

class ApiController extends ResourceController
{
    protected $gedungModel;
    protected $lantaiModel;

    public function __construct()
    {
        $this->gedungModel = new GedungModel();
        $this->lantaiModel = new LantaiModel();
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
}
