<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\OrderDetailModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Produk extends BaseController
{
    private const FIELD = ['category_id', 'nama_produk', 'harga_jual', 'harga_pokok', 'stok', 'unit'];

    public function index()
    {
        $categoryId = (int) $this->request->getGet('category_id') ?: null;
        $keyword    = trim((string) $this->request->getGet('q')) ?: null;

        return view('produk/index', [
            'produk'     => model(ProductModel::class)->getWithCategory($categoryId, $keyword),
            'kategori'   => model(CategoryModel::class)->getAktif(),
            'categoryId' => $categoryId,
            'keyword'    => $keyword,
        ]);
    }

    public function tambah()
    {
        $kategori = model(CategoryModel::class)->getAktif();

        return view('produk/form', [
            'produk'      => null,
            'kategori'    => $kategori,
            'kodeBerikut' => model(ProductModel::class)->kodeBerikutPerKategori($kategori),
        ]);
    }

    public function simpan()
    {
        $model = model(ProductModel::class);
        $data  = $this->request->getPost(self::FIELD);

        $kategori = model(CategoryModel::class)->find((int) ($data['category_id'] ?? 0));
        if (! $kategori || ($kategori['status'] ?? 'aktif') !== 'aktif') {
            return redirect()->back()->withInput()->with('errors', ['category_id' => 'Kategori tidak valid.']);
        }

        $data['kode_produk'] = $model->buatKode($kategori);

        if (! $model->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/produk')->with('success', "Produk {$data['kode_produk']} berhasil ditambahkan.");
    }

    public function edit(int $id)
    {
        return view('produk/form', [
            'produk'      => $this->cari($id),
            'kategori'    => model(CategoryModel::class)->getAktif(),
            'kodeBerikut' => [],
        ]);
    }

    public function update(int $id)
    {
        $model = model(ProductModel::class);
        $lama  = $this->cari($id);
        $data = $this->request->getPost(self::FIELD) + ['id' => $id, 'kode_produk' => $lama['kode_produk']];

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/produk')->with('success', 'Produk berhasil diperbarui.');
    }

    public function hapus(int $id)
    {
        $this->cari($id);

        if (model(OrderDetailModel::class)->where('product_id', $id)->countAllResults() > 0) {
            return redirect()->to('/produk')->with('error', 'Produk sudah pernah terjual dan tidak dapat dihapus.');
        }

        model(ProductModel::class)->delete($id);

        return redirect()->to('/produk')->with('success', 'Produk berhasil dihapus.');
    }

    private function cari(int $id): array
    {
        return model(ProductModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
