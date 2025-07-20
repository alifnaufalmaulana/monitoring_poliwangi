<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        // Cek apakah pengguna sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/dashboard'); // Jika belum login, arahkan ke dashboard
        }

        // Cek apakah peran pengguna sesuai dengan yang diizinkan
        if ($arguments) {
            if (!in_array($role, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
