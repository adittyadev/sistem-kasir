<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session('logged_in')) {
            return redirect()->to($this->beranda(session('role')));
        }

        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $user = model(UserModel::class)->authenticate(
            trim((string) $this->request->getPost('username')),
            (string) $this->request->getPost('password')
        );

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        session()->regenerate(true);
        session()->set([
            'logged_in'    => true,
            'user_id'      => (int) $user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role'         => $user['role'],
        ]);

        return redirect()->to($this->beranda($user['role']));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }

    private function beranda(string $role): string
    {
        return $role === 'admin' ? '/dashboard' : '/transaksi/baru';
    }
}
