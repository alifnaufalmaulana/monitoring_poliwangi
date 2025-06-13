<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerangkatModel;
use App\Models\GedungModel;
use App\Models\RuanganModel;
use App\Models\LantaiModel;
use App\Models\RiwayatModel;

class PerangkatController extends BaseController
{
    protected $perangkatModel;
    protected $gedungModel;
    protected $ruanganModel;
    protected $lantaiModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->gedungModel = new GedungModel();
        $this->ruanganModel = new RuanganModel();
        $this->lantaiModel = new LantaiModel();
        $this->riwayatModel = new RiwayatModel();
    }

    // Tampilkan semua perangkat
    public function index()
    {
        $dataPerangkat = $this->perangkatModel
            ->select('perangkat.*, ruangan.nama_ruangan, lantai.nama_lantai, gedung.nama_gedung')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->join('lantai', 'ruangan.id_lantai = lantai.id_lantai')
            ->join('gedung', 'ruangan.id_gedung = gedung.id_gedung')
            ->findAll();

        $data = [
            'judul' => 'Data Perangkat',
            'perangkat' => $dataPerangkat,
            'gedung' => $this->gedungModel->findAll(),
            'daftar_ruangan' => $this->ruanganModel->findAll(),
        ];

        return view('perangkat', $data);
    }

    // Simpan data perangkat baru
    public function simpan()
    {
        if (!$this->validate([
            'nama_perangkat'   => 'required',
            'id_ruangan'       => 'required|integer',
            'jenis_perangkat'  => 'required',
            'pos_x'            => 'required',
            'pos_y'            => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Pastikan semua field terisi, termasuk posisi pada denah.');
        }

        $data = [
            'nama_perangkat'   => $this->request->getPost('nama_perangkat'),
            'id_ruangan'       => $this->request->getPost('id_ruangan'),
            'jenis_perangkat'  => $this->request->getPost('jenis_perangkat'),
            'latitude'         => null,
            'longitude'        => null,
            'pos_x'            => $this->request->getPost('pos_x'),
            'pos_y'            => $this->request->getPost('pos_y'),
        ];

        $this->perangkatModel->insert($data);

        return redirect()->to('/perangkat')->with('success', 'Data perangkat berhasil disimpan.');
    }


    // Edit data perangkat
    public function edit($id = null)
    {
        $perangkat = $this->perangkatModel->find($id);
        if (!$perangkat) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data perangkat tidak ditemukan.');
        }

        $data = [
            'judul' => 'Edit Perangkat',
            'perangkat' => $perangkat,
        ];

        return view('perangkat_edit', $data);
    }

    // Update data perangkat
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
        ];

        $this->perangkatModel->update($id, $data);

        return redirect()->to('/perangkat')->with('success', 'Data perangkat berhasil diupdate.');
    }

    // Hapus perangkat
    public function hapus($id = null)
    {
        if ($id == null || !$this->perangkatModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data perangkat tidak ditemukan.');
        }

        $this->perangkatModel->delete($id);
        return redirect()->to('/perangkat')->with('success', 'Data perangkat berhasil dihapus.');
    }

    // Ambil data lantai berdasarkan gedung (untuk dropdown dinamis)
    public function getLantai($idGedung)
    {
        $lantai = $this->lantaiModel->where('id_gedung', $idGedung)->findAll();
        return $this->response->setJSON($lantai);
    }

    // Ambil data ruangan berdasarkan lantai (untuk dropdown dinamis)
    public function getRuangan($idLantai)
    {
        $ruangan = $this->ruanganModel->where('id_lantai', $idLantai)->findAll();
        return $this->response->setJSON($ruangan);
    }

    // Ambil denah berdasarkan id lantai
    public function getDenah($id_lantai)
    {
        $lantai = $this->lantaiModel->find($id_lantai);

        if ($lantai) {
            return $this->response->setJSON(['denah' => $lantai['denah']]);
        } else {
            return $this->response->setJSON(['denah' => null]);
        }
    }

    public function getPerangkatByLantai($id_lantai)
    {
        $perangkatModel = new PerangkatModel();
        $riwayatModel = new RiwayatModel();

        // Ambil semua perangkat berdasarkan lantai
        $perangkatList = $perangkatModel
            ->where('id_lantai', $id_lantai)
            ->findAll();

        // Loop tiap perangkat untuk tambahkan status terbaru
        foreach ($perangkatList as &$perangkat) {
            $lastStatus = $riwayatModel
                ->where('id_perangkat', $perangkat['id_perangkat'])
                ->orderBy('waktu', 'DESC')
                ->first();

            // Jika tidak ditemukan, default 'tidak diketahui'
            $perangkat['status_perangkat'] = $lastStatus['status_perangkat'] ?? 'tidak diketahui';
        }

        // Kirim data JSON ke frontend
        return $this->response->setJSON($perangkatList);
    }
}
