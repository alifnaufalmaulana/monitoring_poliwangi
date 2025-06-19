<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new \App\Models\PenggunaModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'role' => $user['role'],
                'logged_in' => true
            ]);
            return redirect()->to('/home');
        }

        return redirect()->back()->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/home');
    }
}
