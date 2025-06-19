<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GedungModel;
use App\Models\LantaiModel;
use App\Models\RuanganModel;
use App\Models\PerangkatModel;
use App\Models\LaporanModel;
use App\Models\KebencanaanModel;

class LaporanController extends BaseController
{
    protected $laporanModel;
    protected $gedungModel;
    protected $lantaiModel;
    protected $ruanganModel;
    protected $perangkatModel;
    protected $kebencanaanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        $this->gedungModel = new GedungModel();
        $this->lantaiModel = new LantaiModel();
        $this->ruanganModel = new RuanganModel();
        $this->perangkatModel = new PerangkatModel();
        $this->kebencanaanModel = new KebencanaanModel();
    }

    // List semua laporan (Read)
    public function index()
    {
        // Mengambil data laporan dengan join ke tabel terkait untuk menampilkan nama lokasi
        $laporan = $this->laporanModel
            ->select('laporan.*, 
                      perangkat.nama_perangkat,
                      ruangan.nama_ruangan,    
                      lantai.nama_lantai,      
                      gedung.nama_gedung')     // Komentar dihapus dari sini
            ->join('perangkat', 'perangkat.id_perangkat = laporan.id_perangkat', 'left')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan', 'left')
            ->join('lantai', 'lantai.id_lantai = ruangan.id_lantai', 'left')
            ->join('gedung', 'gedung.id_gedung = ruangan.id_gedung', 'left')
            ->orderBy('laporan.waktu_laporan', 'DESC')
            ->findAll();

        $data = [
            'laporan' => $laporan,
            'judul' => 'Pelaporan',
            'gedung' => $this->gedungModel->findAll(),
            'lantai' => $this->lantaiModel->findAll(),
            'ruangan' => $this->ruanganModel->findAll(),
            'perangkat' => $this->perangkatModel->findAll(),
            'daftar_kebencanaan' => $this->kebencanaanModel->getJenisBencanaUnik(),
            'waktu_laporan'  => date('Y-m-d H:i:s'),
        ];

        return view('laporan', $data);
    }

    // Metode untuk menangani simpan data baru atau update data yang sudah ada
    public function simpan()
    {
        $id_laporan = $this->request->getPost('id_laporan');

        // Aturan validasi
        $rules = [
            'id_perangkat'   => 'required|integer',
            'nama_bencana'  => 'required|string|max_length[255]',
            'status_bencana' => 'required|string|max_length[50]',
            'deskripsi'      => 'required|string',
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan error JSON
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Validasi Gagal!',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $dataToSave = [
            'id_perangkat'   => $this->request->getPost('id_perangkat'),
            'nama_bencana'  => $this->request->getPost('nama_bencana'),
            'status_bencana' => $this->request->getPost('status_bencana'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
        ];

        if (empty($id_laporan)) {
            // Jika id_laporan kosong, berarti ini adalah data baru (tambah)
            $this->laporanModel->insert($dataToSave);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Laporan berhasil ditambahkan!'
            ]);
        } else {
            // Jika id_laporan ada, berarti ini adalah update
            return $this->update($id_laporan);
        }
    }

    // Mengambil data laporan tunggal untuk ditampilkan di form edit
    public function edit($id_laporan)
    {
        $laporan = $this->laporanModel
            ->select('laporan.*, 
                      perangkat.nama_perangkat, ')
            ->join('perangkat', 'perangkat.id_perangkat = laporan.id_perangkat')
            ->find($id_laporan);

        if ($laporan) {
            return $this->response->setJSON($laporan);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Laporan tidak ditemukan.'
            ]);
        }
    }

    // Metode untuk update data laporan (dipanggil dari simpan() jika id_laporan ada)
    public function update($id_laporan)
    {
        // Validasi sudah dilakukan di method simpan()
        $dataToUpdate = [
            'id_perangkat'   => $this->request->getPost('id_perangkat'),
            'nama_bencana'  => $this->request->getPost('nama_bencana'),
            'status_bencana' => $this->request->getPost('status_bencana'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
        ];

        $this->laporanModel->update($id_laporan, $dataToUpdate);
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Laporan berhasil diupdate!'
        ]);
    }

    // Hapus laporan (Delete)
    public function hapus($id_laporan)
    {
        if ($this->laporanModel->delete($id_laporan)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Laporan berhasil dihapus!'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus laporan.'
            ]);
        }
    }
}
