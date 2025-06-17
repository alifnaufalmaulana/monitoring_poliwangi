<?php

namespace App\Models;

use CodeIgniter\Model;

class KebencanaanModel extends Model
{
    protected $table = 'kebencanaan';
    protected $primaryKey = 'id_kebencanaan';
    protected $allowedFields = ['id_perangkat','jenis_bencana', 'waktu'];
}
