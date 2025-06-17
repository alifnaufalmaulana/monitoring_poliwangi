<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RiwayatModel;
use App\Models\GedungModel;
use App\Models\LantaiModel;
use CodeIgniter\HTTP\ResponseInterface;

class RiwayatController extends BaseController
{
    public function index()
    {
        $riwayatModel = new RiwayatModel();
        $gedungModel = new GedungModel();
        $lantaiModel = new LantaiModel();

        // Ambil parameter dari GET
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $aksi = $this->request->getGet('aksi');
        $id_gedung = $this->request->getGet('id_gedung');
        $id_lantai = $this->request->getGet('id_lantai');

        // Buat query awal
        $query = $riwayatModel->select('riwayat_perangkat.*, perangkat.nama_perangkat, ruangan.nama_ruangan, lantai.nama_lantai, gedung.nama_gedung')
            ->join('perangkat', 'perangkat.id_perangkat = riwayat_perangkat.id_perangkat')
            ->join('ruangan', 'ruangan.id_ruangan = perangkat.id_ruangan')
            ->join('lantai', 'lantai.id_lantai = ruangan.id_lantai')
            ->join('gedung', 'gedung.id_gedung = lantai.id_gedung')
            ->orderBy('waktu', 'DESC');

        // Filter berdasarkan tanggal
        if ($tanggal_awal && $tanggal_akhir) {
            $query->where("DATE(riwayat_perangkat.waktu) >=", $tanggal_awal)
                ->where("DATE(riwayat_perangkat.waktu) <=", $tanggal_akhir);
        }

        // Filter berdasarkan aksi
        if ($aksi) {
            $query->where('riwayat_perangkat.aksi', $aksi);
        }

        // Filter berdasarkan gedung
        if ($id_gedung) {
            $query->where('gedung.id_gedung', $id_gedung);
        }

        // Filter berdasarkan lantai
        if ($id_lantai) {
            $query->where('lantai.id_lantai', $id_lantai);
        }

        // Ambil data hasil query
        $data = [
            'judul' => 'Riwayat Perangkat',
            'riwayat' => $query->findAll(),
            'gedung' => $gedungModel->findAll(),
            'lantai' => $lantaiModel->findAll(),
        ];

        // Kirim data gedung dan lantai untuk keperluan dropdown filter
        $data['gedung'] = $gedungModel->findAll();
        $data['lantai'] = $lantaiModel->findAll();

        return view('riwayat', $data);
    }
}
