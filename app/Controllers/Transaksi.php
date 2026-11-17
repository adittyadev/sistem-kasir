<?php

namespace App\Controllers;

use App\Models\OrderDetailModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Transaksi extends BaseController
{
    public function index()
    {
        $userId = session('role') === 'kasir' ? (int) session('user_id') : null;

        return view('transaksi/index', ['orders' => model(OrderModel::class)->getRiwayat($userId)]);
    }

    public function baru()
    {
        return view('transaksi/baru', ['produk' => model(ProductModel::class)->getTersedia()]);
    }

    public function simpan()
    {
        $rules = [
            'tipe_pesanan'   => 'required|in_list[dine_in,takeaway]',
            'nomor_meja'     => 'permit_empty|max_length[10]',
            'nama_pemesan'   => 'permit_empty|max_length[100]',
            'metode_bayar'   => 'required|in_list[tunai,qris,transfer,debit]',
            'diskon_nominal' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'uang_dibayar'   => 'permit_empty|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $produk  = (array) $this->request->getPost('product_id');
        $qty     = (array) $this->request->getPost('kuantiti');
        $catatan = (array) $this->request->getPost('catatan_khusus');
        $items   = [];

        foreach ($produk as $i => $productId) {
            if ($productId === '' || (int) ($qty[$i] ?? 0) < 1) {
                continue;
            }
            $items[] = [
                'product_id'     => (int) $productId,
                'kuantiti'       => (int) $qty[$i],
                'catatan_khusus' => trim((string) ($catatan[$i] ?? '')) ?: null,
            ];
        }

        $header = $this->request->getPost(['tipe_pesanan', 'nomor_meja', 'nama_pemesan', 'metode_bayar', 'diskon_nominal', 'uang_dibayar']);
        $header['user_id'] = (int) session('user_id');

        try {
            $orderId = model(OrderModel::class)->buatPesanan($header, $items);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to("/transaksi/{$orderId}")->with('success', 'Transaksi berhasil disimpan.');
    }

    public function detail(int $id)
    {
        $order = $this->cari($id);

        return view('transaksi/detail', [
            'order'  => $order,
            'detail' => model(OrderDetailModel::class)->getByOrder($id),
        ]);
    }

    public function struk(int $id)
    {
        $order  = $this->cari($id);
        $ukuran = in_array($this->request->getGet('ukuran'), ['58', '80', 'a4'], true)
            ? $this->request->getGet('ukuran')
            : '80';

        return view('transaksi/struk', [
            'order'  => $order,
            'detail' => model(OrderDetailModel::class)->getByOrder($id),
            'ukuran' => $ukuran,
        ]);
    }

    public function status(int $id)
    {
        $this->cari($id);

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['diproses', 'selesai', 'dibatalkan'], true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        try {
            model(OrderModel::class)->ubahStatus($id, $status);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to("/transaksi/{$id}")->with('success', 'Status pesanan diperbarui.');
    }

    private function cari(int $id): array
    {
        $order = model(OrderModel::class)->getDetail($id);

        if ($order === null || (session('role') === 'kasir' && (int) $order['user_id'] !== (int) session('user_id'))) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $order;
    }
}
