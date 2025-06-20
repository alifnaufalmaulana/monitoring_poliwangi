<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    protected $allowedFields = ['id_perangkat', 'nama_bencana', 'deskripsi', 'status_bencana'];
}
