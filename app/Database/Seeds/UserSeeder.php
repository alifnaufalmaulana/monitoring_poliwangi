<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'username' => 'keamanan',
                'password' => password_hash('keamanan123', PASSWORD_DEFAULT),
                'role'     => 'keamanan',
            ]
        ];

        $this->db->table('pengguna')->insertBatch($data);
    }
}
