<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function login()
    {
        return view('auth/login');
    }

    // Proses login pengguna
    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new \App\Models\PenggunaModel();
        $user = $userModel->where('username', $username)->first();

        // Verifikasi user dan password
        if ($user && password_verify($password, $user['password'])) {
            // Set session pengguna
            session()->set([
                'id_user'   => $user['id_user'],
                'username'  => $user['username'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);
            return redirect()->to('/home');
        }

        // Jika gagal login
        return redirect()->back()->with('error', 'Username atau password salah');
    }

    // Logout pengguna
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/home');
    }
}
