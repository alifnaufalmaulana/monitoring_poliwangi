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

        if (!session()->get('logged_in')) {
            return redirect()->to('/dashboard'); // publik diarahkan ke dashboard
        }

        if ($arguments) {
            if (!in_array($role, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
