<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerangkatModel;
use App\Models\GedungModel;
use App\Models\RuanganModel;

class PerangkatController extends BaseController
{
    protected $perangkatModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
    }

    // Tampilkan semua perangkat
    public function index()
    {
        $model = new PerangkatModel();
        $gedungModel = new GedungModel(); // tambahkan ini
        $ruanganModel = new RuanganModel(); // untuk form

        $dataPerangkat = $model
            ->select('perangkat.*, ruangan.nama_ruangan, lantai.nama_lantai, gedung.nama_gedung')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->join('lantai', 'ruangan.id_lantai = lantai.id_lantai')
            ->join('gedung', 'ruangan.id_gedung = gedung.id_gedung')
            ->findAll();

        $data = [
            'judul' => 'Data Perangkat',
            'perangkat' => $dataPerangkat,
            'gedung' => $gedungModel->findAll(),
            'daftar_ruangan' => $ruanganModel->findAll(), // jika masih pakai ini
        ];

        return view('perangkat', $data);
    }

    // Simpan data perangkat baru (Create)
    public function simpan()
    {
        // Validasi sederhana
        if (!$this->validate([
            'nama_perangkat' => 'required',
            'id_ruangan' => 'required|integer',
            'jenis_perangkat' => 'required',
        ])) {
            // Kalau gagal validasi bisa redirect dengan input lama
            return redirect()->back()->withInput()->with('error', 'Data tidak valid.');
        }

        $data = [
            'nama_perangkat' => $this->request->getPost('nama_perangkat'),
            'id_ruangan' => $this->request->getPost('id_ruangan'),
            'jenis_perangkat' => $this->request->getPost('jenis_perangkat'),
            'latitude' => null,    // bisa diisi jika ada data
            'longitude' => null,   // bisa diisi jika ada data
            'update_at' => date('Y-m-d H:i:s'),
        ];

        $this->perangkatModel->insert($data);

        return redirect()->to('/perangkat')->with('success', 'Data perangkat berhasil disimpan.');
    }

    // Form edit (optional, kalau pakai form terpisah)
    public function edit($id = null)
    {
        $perangkat = $this->perangkatModel->find($id);
        if (!$perangkat) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data perangkat tidak ditemukan.');
        }

        $data = [
            'judul' => 'Edit Perangkat',
            'perangkat' => $perangkat,
            // 'daftar_ruangan' => $this->getDaftarRuangan(),
        ];

        return view('perangkat_edit', $data);
    }

    // Update data perangkat (Update)
    public function update($id = null)
    {
        if (!$this->validate([
            'nama_perangkat' => 'required',
            'id_ruangan' => 'required|integer',
            'jenis_perangkat' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid.');
        }

        $data = [
            'nama_perangkat' => $this->request->getPost('nama_perangkat'),
            'id_ruangan' => $this->request->getPost('id_ruangan'),
            'jenis_perangkat' => $this->request->getPost('jenis_perangkat'),
            'update_at' => date('Y-m-d H:i:s'),
        ];

        $this->perangkatModel->update($id, $data);

        return redirect()->to('/perangkat')->with('success', 'Data perangkat berhasil diupdate.');
    }

    // Hapus perangkat (Delete)
    public function hapus($id = null)
    {
        if ($id == null || !$this->perangkatModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data perangkat tidak ditemukan.');
        }

        $this->perangkatModel->delete($id);
        return redirect()->to('/perangkat')->with('success', 'Data perangkat berhasil dihapus.');
    }

    public function getLantai($idGedung)
    {
        $lantaiModel = new \App\Models\LantaiModel();
        $lantai = $lantaiModel->where('id_gedung', $idGedung)->findAll();

        return $this->response->setJSON($lantai);
    }

    public function getRuangan($idLantai)
    {
        $ruanganModel = new \App\Models\RuanganModel();
        $ruangan = $ruanganModel->where('id_lantai', $idLantai)->findAll();

        return $this->response->setJSON($ruangan);
    }
}
