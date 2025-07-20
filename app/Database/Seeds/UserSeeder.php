<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Data akun pengguna awal yang akan dimasukkan ke tabel 'pengguna'
        $data = [
            [
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT), // Enkripsi password
                'role'     => 'admin',
            ],
            [
                'username' => 'keamanan',
                'password' => password_hash('keamanan123', PASSWORD_DEFAULT), // Enkripsi password
                'role'     => 'keamanan',
            ]
        ];

        // Insert semua data sekaligus ke dalam tabel 'pengguna'
        $this->db->table('pengguna')->insertBatch($data);
    }
}
