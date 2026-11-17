<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class User extends BaseController
{
    private const FIELD = ['username', 'password', 'nama_lengkap', 'role', 'status'];

    public function index()
    {
        return view('user/index', ['users' => model(UserModel::class)->orderBy('nama_lengkap')->findAll()]);
    }

    public function tambah()
    {
        return view('user/form', ['user' => null]);
    }

    public function simpan()
    {
        $model = model(UserModel::class);
        $model->setValidationRule('password', 'required|min_length[6]');

        if (! $model->insert($this->request->getPost(self::FIELD))) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/user')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return view('user/form', ['user' => $this->cari($id)]);
    }

    public function update(int $id)
    {
        $model = model(UserModel::class);
        $user  = $this->cari($id);
        $data  = $this->request->getPost(self::FIELD);

        if ($id === (int) session('user_id')) {
            $data['role']   = $user['role'];
            $data['status'] = $user['status'];
        }

        $model->setValidationRule('password', 'permit_empty|min_length[6]');

        if (! $model->save($data + ['id' => $id])) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/user')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    private function cari(int $id): array
    {
        return model(UserModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
