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
        $this->perangkatModel = new PerangkatModel();
        $this->gedungModel = new GedungModel();
        $this->ruanganModel = new RuanganModel();
        $this->lantaiModel = new LantaiModel();
        $this->riwayatModel = new RiwayatModel();
        $this->kebencanaanModel = new KebencanaanModel();
    }

    public function index()
    {
        $dataPerangkat = $this->perangkatModel
            ->select('perangkat.*, ruangan.nama_ruangan, lantai.nama_lantai, gedung.nama_gedung')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->join('lantai', 'ruangan.id_lantai = lantai.id_lantai')
            ->join('gedung', 'lantai.id_gedung = gedung.id_gedung')
            ->findAll();

        $data = [
            'judul' => 'Data Perangkat',
            'perangkat' => $dataPerangkat,
            'gedung' => $this->gedungModel->findAll(),
        ];

        return view('perangkat', $data);
    }

    // Metode simpan() sekarang hanya untuk INSERT baru
    public function simpan()
    {
        // Debugging: Lihat data yang diterima dari POST
        // log_message('debug', 'Data diterima untuk INSERT: ' . json_encode($this->request->getPost()));

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

        $data = [
            'nama_perangkat'  => $this->request->getPost('nama_perangkat'),
            'id_ruangan'      => $this->request->getPost('id_ruangan'),
            'jenis_perangkat' => $this->request->getPost('jenis_perangkat'),
            'pos_x'           => $this->request->getPost('pos_x'),
            'pos_y'           => $this->request->getPost('pos_y'),
            'waktu'           => date('Y-m-d H:i:s'), // Waktu sekarang
        ];

        if ($this->perangkatModel->insert($data)) { // Gunakan insert() khusus untuk INSERT
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

    // Metode update($id) sekarang khusus untuk UPDATE
    public function update($id)
    {
        // Debugging: Lihat data yang diterima untuk UPDATE
        // log_message('debug', 'Data diterima untuk UPDATE: ' . json_encode($this->request->getPost()));

        $validationRules = [
            'nama_perangkat'   => 'required',
            'id_gedung'        => 'required', // Tetap validasi ini jika Anda membutuhkannya
            'id_lantai'        => 'required', // Tetap validasi ini jika Anda membutuhkannya
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
            'waktu'            => date('Y-m-d H:i:s'), // Update waktu juga
        ];

        // CodeIgniter's update method: update(ID, DATA)
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

    public function hapus($id)
    {
        $perangkat = $this->perangkatModel->find($id);
        if (!$perangkat) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Perangkat tidak ditemukan.']);
        }

        try {
            $this->kebencanaanModel->where('id_perangkat', $id)->delete();
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data terkait di tabel kebencanaan: ' . $e->getMessage()]);
        }

        try {
            $this->riwayatModel->where('id_perangkat', $id)->delete();
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus riwayat perangkat: ' . $e->getMessage()]);
        }

        if ($this->perangkatModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Perangkat berhasil dihapus beserta data terkaitnya.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus perangkat utama.']);
        }
    }

    public function getLantai($idGedung)
    {
        $lantai = $this->lantaiModel->where('id_gedung', $idGedung)->findAll();
        return $this->response->setJSON($lantai);
    }

    public function getRuangan($id_lantai)
    {
        $ruangan = $this->ruanganModel->where('id_lantai', $id_lantai)->findAll();
        return $this->response->setJSON($ruangan);
    }

    public function getDenah($id_lantai)
    {
        $lantai = $this->lantaiModel->find($id_lantai);

        if ($lantai && isset($lantai['denah'])) {
            return $this->response->setJSON(['denah' => $lantai['denah']]);
        } else {
            return $this->response->setJSON(['denah' => null]);
        }
    }

    public function getPerangkatByLantai($id_lantai)
    {
        $perangkatList = $this->perangkatModel
            ->select('perangkat.*, ruangan.id_lantai')
            ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
            ->where('ruangan.id_lantai', $id_lantai)
            ->findAll();

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
