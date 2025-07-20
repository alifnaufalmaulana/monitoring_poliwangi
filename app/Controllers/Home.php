<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GedungModel;
use App\Models\PerangkatModel;
use App\Models\RiwayatModel;

class Home extends BaseController
{
    public function index(): string
    {
        $gedungModel    = new GedungModel();
        $perangkatModel = new PerangkatModel();
        $riwayatModel   = new RiwayatModel();

        // Ambil semua gedung
        $dataGedung = $gedungModel
            ->select('gedung.id_gedung, gedung.nama_gedung, gedung.latitude, gedung.longitude, gedung.tipe')
            ->findAll();

        foreach ($dataGedung as &$gedung) {
            // Ambil perangkat berdasarkan gedung (melalui join ruangan dan lantai)
            $perangkatList = $perangkatModel
                ->select('perangkat.id_perangkat')
                ->join('ruangan', 'perangkat.id_ruangan = ruangan.id_ruangan')
                ->join('lantai', 'ruangan.id_lantai = lantai.id_lantai')
                ->where('lantai.id_gedung', $gedung['id_gedung'])
                ->findAll();

            $perangkatIds = array_column($perangkatList, 'id_perangkat');

            // Ambil status terbaru dari masing-masing perangkat
            $riwayatList = [];
            if (!empty($perangkatIds)) {
                $riwayatAll = $riwayatModel
                    ->whereIn('id_perangkat', $perangkatIds)
                    ->orderBy('waktu', 'DESC')
                    ->findAll();

                // Hanya simpan status terakhir dari tiap perangkat
                foreach ($riwayatAll as $r) {
                    $pid = $r['id_perangkat'];
                    if (!isset($riwayatList[$pid])) {
                        $riwayatList[$pid] = $r;
                    }
                }
            }

            $statusGedung = 'aktif';

            // Jika ada perangkat status "bahaya", status gedung juga dianggap bahaya
            foreach ($perangkatIds as $pid) {
                if (isset($riwayatList[$pid]) && $riwayatList[$pid]['status_perangkat'] === 'bahaya') {
                    $statusGedung = 'bahaya';
                    break;
                }
            }

            $gedung['status_gedung'] = $statusGedung;
        }

        $data = [
            'judul'  => 'Peta Dashboard',
            'gedung' => $dataGedung,
        ];

        return view('dashboard', $data);
    }
}
