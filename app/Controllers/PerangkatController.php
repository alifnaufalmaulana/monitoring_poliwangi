<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerangkatModel;
use App\Models\GedungModel;
use App\Models\RuanganModel;
use App\Models\LantaiModel;
use App\Models\RiwayatModel;
use App\Models\KebencanaanModel;

class PerangkatController extends BaseController
{
    protected $perangkatModel;
    protected $gedungModel;
    protected $ruanganModel;
    protected $lantaiModel;
    protected $riwayatModel;
    protected $kebencanaanModel;

    public function __construct()
    {
        // Inisialisasi model yang digunakan
        $this->perangkatModel = new PerangkatModel();
        $this->gedungModel = new GedungModel();
        $this->ruanganModel = new RuanganModel();
        $this->lantaiModel = new LantaiModel();
        $this->riwayatModel = new RiwayatModel();
        $this->kebencanaanModel = new KebencanaanModel();
    }

    // Menampilkan halaman daftar perangkat 
    public function index()
    {
        $gedungId  = $this->request->getGet('id_gedung');
        $lantaiId  = $this->request->getGet('id_lantai');
        $ruanganId = $this->request->getGet('id_ruangan');

        $perangkat = $this->perangkatModel->getFilterPerangkat($gedungId, $lantaiId, $ruanganId);

        $data = [
            'judul'     => 'Perangkat',
            'perangkat' => $perangkat,
            'gedung'    => $this->gedungModel->findAll(),
            'lantai'    => $this->lantaiModel->findAll(),
            'ruangan'   => $this->ruanganModel->findAll(),
        ];

        return view('perangkat', $data);
    }

    // Menyimpan data perangkat baru
    public function simpan()
    {
        // Validasi input
        if (!$this->validate([
            'nama_perangkat' => 'required',
            'id_gedung' => 'required',
            'id_lantai' => 'required',
            'id_ruangan' => 'required',
            'jenis_perangkat' => 'required',
            'pos_x' => 'required',
            'pos_y' => 'required',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal. Lengkapi semua data.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Simpan ke database
        $data = [
            'nama_perangkat'  => $this->request->getPost('nama_perangkat'),
            'id_ruangan'      => $this->request->getPost('id_ruangan'),
            'jenis_perangkat' => $this->request->getPost('jenis_perangkat'),
            'pos_x'           => $this->request->getPost('pos_x'),
            'pos_y'           => $this->request->getPost('pos_y'),
            'waktu'           => date('Y-m-d H:i:s'),
        ];

        if ($this->perangkatModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Perangkat berhasil ditambahkan.',
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan perangkat.',
                'errors' => $this->perangkatModel->errors()
            ]);
        }
    }

    // Mengambil detail perangkat untuk form edit
    public function getPerangkat($id)
    {
        $perangkat = $this->perangkatModel
            ->select('perangkat.*, ruangan.id_lantai, lantai.id_gedung')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->join('lantai', 'ruangan.id_lantai = lantai.id_lantai')
            ->where('perangkat.id_perangkat', $id)
            ->first();

        return $this->response->setJSON($perangkat);
    }

    // Memperbarui data perangkat berdasarkan ID
    public function update($id)
    {
        $validationRules = [
            'nama_perangkat'   => 'required',
            'id_gedung'        => 'required',
            'id_lantai'        => 'required',
            'id_ruangan'       => 'required',
            'jenis_perangkat'  => 'required',
            'pos_x'            => 'required',
            'pos_y'            => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal. Lengkapi semua data.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'nama_perangkat'   => $this->request->getPost('nama_perangkat'),
            'id_ruangan'       => $this->request->getPost('id_ruangan'),
            'jenis_perangkat'  => $this->request->getPost('jenis_perangkat'),
            'pos_x'            => $this->request->getPost('pos_x'),
            'pos_y'            => $this->request->getPost('pos_y'),
            'waktu'            => date('Y-m-d H:i:s'),
        ];

        if ($this->perangkatModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Perangkat berhasil diupdate!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate perangkat.',
                'errors' => $this->perangkatModel->errors()
            ]);
        }
    }

    // Menghapus perangkat dan data terkait
    public function hapus($id)
    {
        $perangkat = $this->perangkatModel->find($id);
        if (!$perangkat) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Perangkat tidak ditemukan.']);
        }

        // Hapus data terkait di tabel kebencanaan
        try {
            $this->kebencanaanModel->where('id_perangkat', $id)->delete();
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data terkait di tabel kebencanaan: ' . $e->getMessage()]);
        }

        // Hapus riwayat perangkat
        try {
            $this->riwayatModel->where('id_perangkat', $id)->delete();
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus riwayat perangkat: ' . $e->getMessage()]);
        }

        // Hapus perangkat
        if ($this->perangkatModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Perangkat berhasil dihapus.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus perangkat utama.']);
        }
    }

    // Mendapatkan daftar lantai dari gedung tertentu
    public function getLantai($idGedung)
    {
        $lantai = $this->lantaiModel->where('id_gedung', $idGedung)->findAll();
        return $this->response->setJSON($lantai);
    }

    // Mendapatkan daftar ruangan dari lantai tertentu
    public function getRuangan($id_lantai)
    {
        $ruangan = $this->ruanganModel->where('id_lantai', $id_lantai)->findAll();
        return $this->response->setJSON($ruangan);
    }

    // Mengambil denah dari lantai tertentu
    public function getDenah($id_lantai)
    {
        $lantai = $this->lantaiModel->find($id_lantai);

        if ($lantai && isset($lantai['denah'])) {
            return $this->response->setJSON(['denah' => $lantai['denah']]);
        } else {
            return $this->response->setJSON(['denah' => null]);
        }
    }

    // Menampilkan perangkat-perangkat berdasarkan lantai di denah lantai 
    public function getPerangkatByLantai($id_lantai)
    {
        $perangkatList = $this->perangkatModel
            ->select('perangkat.*, ruangan.id_lantai')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->where('ruangan.id_lantai', $id_lantai)
            ->findAll();

        // Ambil status terakhir dari setiap perangkat
        foreach ($perangkatList as &$perangkat) {
            $lastStatus = $this->riwayatModel
                ->where('id_perangkat', $perangkat['id_perangkat'])
                ->orderBy('waktu', 'DESC')
                ->first();

            $perangkat['status_perangkat'] = $lastStatus['status_perangkat'] ?? 'tidak diketahui';
        }

        return $this->response->setJSON($perangkatList);
    }
}
