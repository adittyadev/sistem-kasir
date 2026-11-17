<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Kategori extends BaseController
{
    public function index()
    {
        return view('kategori/index', ['kategori' => model(CategoryModel::class)->orderBy('nama_kategori')->findAll()]);
    }

    public function tambah()
    {
        return view('kategori/form', ['kategori' => null]);
    }

    public function simpan()
    {
        $model = model(CategoryModel::class);

        if (! $model->insert($this->input())) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return view('kategori/form', ['kategori' => $this->cari($id)]);
    }

    public function update(int $id)
    {
        $model = model(CategoryModel::class);
        $this->cari($id);

        if (! $model->save($this->input() + ['id' => $id])) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function hapus(int $id)
    {
        $this->cari($id);

        if (model(ProductModel::class)->where('category_id', $id)->countAllResults() > 0) {
            return redirect()->to('/kategori')->with('error', 'Kategori masih dipakai oleh produk dan tidak dapat dihapus.');
        }

        model(CategoryModel::class)->delete($id);

        return redirect()->to('/kategori')->with('success', 'Kategori berhasil dihapus.');
    }

    private function input(): array
    {
        $nama = trim((string) $this->request->getPost('nama_kategori'));

        return [
            'nama_kategori' => $nama,
            'slug'          => url_title($nama, '-', true),
            'status'        => $this->request->getPost('status'),
        ];
    }

    private function cari(int $id): array
    {
        return model(CategoryModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
