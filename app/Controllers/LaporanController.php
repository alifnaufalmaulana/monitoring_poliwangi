<?php

// namespace App\Controllers;

// use App\Controllers\BaseController;
// use App\Models\LaporanModel;
// use CodeIgniter\HTTP\ResponseInterface;

// class LaporanController extends BaseController
// {
//     public function index(): string
//     {
//         $model = new LaporanModel();
//         $dataLaporan = $model
//             ->select('laporan.*')
//             //->orderBy('waktu', 'DESC')
//             ->findAll();

//         $data = [
//             'judul'   => 'Data Laporan Bencana',
//             'laporan' => $dataLaporan,
//         ];

//         return view('laporan', $data);
//     }
// }

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LaporanModel;
use App\Models\KebencanaanModel;

class LaporanController extends BaseController
{
    protected $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
    }

    // List semua laporan (Read)
    public function index()
    {
        $laporanModel = new LaporanModel();
        $kebencanaanModel = new KebencanaanModel();

        $data = [
            'laporan' => $laporanModel
                ->select('laporan.*, kebencanaan.jenis_bencana')
                ->join('kebencanaan', 'kebencanaan.id_kebencanaan = laporan.id_kebencanaan')
                ->findAll(),

            'judul' => 'Data Pelaporan',
            'daftar_kebencanaan' => $kebencanaanModel->findAll()
        ];

        return view('laporan', $data);
    }

    // Tampilkan form tambah laporan (Create form)
    public function create()
    {
        $data = [
            'judul' => 'Tambah Laporan Bencana',
            'validation' => \Config\Services::validation()
        ];
        return view('laporan/create', $data);
    }

    // Simpan data laporan baru (Create process)
    public function store()
    {
        if (!$this->validate([
            'id_kebencanaan' => 'required|integer',
            'nama_bencana'   => 'required|string|max_length[255]',
            'deskripsi'      => 'required|string',
            'status_bencana' => 'required|string|max_length[50]',
        ])) {
            return redirect()->back()->withInput();
        }

        $this->laporanModel->save([
            'id_kebencanaan' => $this->request->getPost('id_kebencanaan'),
            'nama_bencana'   => $this->request->getPost('nama_bencana'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'status_bencana' => $this->request->getPost('status_bencana'),
        ]);

        return redirect()->to('/laporan')->with('success', 'Laporan berhasil ditambahkan.');
    }

    // Tampilkan form edit laporan (Update form)
    public function edit($id_laporan)
    {
        $laporan = $this->laporanModel->find($id_laporan);
        if (!$laporan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Laporan dengan ID $id_laporan tidak ditemukan");
        }

        $data = [
            'judul' => 'Edit Laporan Bencana',
            'laporan' => $laporan,
            'validation' => \Config\Services::validation()
        ];

        return view('laporan/edit', $data);
    }

    // Update data laporan (Update process)
    public function update($id_laporan)
    {
        if (!$this->validate([
            'id_kebencanaan' => 'required|integer',
            'nama_bencana'   => 'required|string|max_length[255]',
            'deskripsi'      => 'required|string',
            'status_bencana' => 'required|string|max_length[50]',
        ])) {
            return redirect()->back()->withInput();
        }

        $this->laporanModel->update($id_laporan, [
            'id_kebencanaan' => $this->request->getPost('id_kebencanaan'),
            'nama_bencana'   => $this->request->getPost('nama_bencana'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'status_bencana' => $this->request->getPost('status_bencana'),
        ]);

        return redirect()->to('/laporan')->with('success', 'Laporan berhasil diupdate.');
    }

    // Hapus laporan (Delete)
    public function delete($id_laporan)
    {
        $this->laporanModel->delete($id_laporan);
        return redirect()->to('/laporan')->with('success', 'Laporan berhasil dihapus.');
    }
}
