<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;
use Throwable;

class OrderModel extends Model
{
    public const TARIF_PAJAK = 0.10;

    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'invoice_number',
        'user_id',
        'tipe_pesanan',
        'nomor_meja',
        'nama_pemesan',
        'total_subtotal',
        'pajak_nominal',
        'diskon_nominal',
        'grand_total',
        'metode_bayar',
        'uang_dibayar',
        'uang_kembali',
        'status',
    ];

    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-POS-' . date('Ymd') . '-';

        $last = $this->db->query(
            'SELECT invoice_number FROM orders WHERE invoice_number LIKE ? ORDER BY invoice_number DESC LIMIT 1 FOR UPDATE',
            [$prefix . '%']
        )->getRowArray();

        $next = $last ? ((int) substr($last['invoice_number'], -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function buatPesanan(array $header, array $items): int
    {
        if ($items === []) {
            throw new RuntimeException('Pesanan belum memiliki item.');
        }
        if ($header['tipe_pesanan'] === 'dine_in' && empty($header['nomor_meja'])) {
            throw new RuntimeException('Nomor meja wajib diisi untuk Dine-In.');
        }

        $productModel = model(ProductModel::class);

        $this->db->transBegin();

        try {
            $details  = [];
            $subtotal = 0;

            foreach ($items as $item) {
                $qty = (int) $item['kuantiti'];
                if ($qty < 1) {
                    throw new RuntimeException('Kuantiti minimal 1.');
                }

                $product = $productModel->lockById((int) $item['product_id']);
                if ($product === null) {
                    throw new RuntimeException('Menu tidak ditemukan.');
                }
                if (! $productModel->kurangiStok((int) $product['id'], $qty)) {
                    throw new RuntimeException("Stok {$product['nama_produk']} tidak cukup (sisa {$product['stok']}).");
                }

                $harga     = (float) $product['harga_jual'];
                $line      = $harga * $qty;
                $subtotal += $line;

                $details[] = [
                    'product_id'     => $product['id'],
                    'harga_satuan'   => $harga,
                    'kuantiti'       => $qty,
                    'subtotal_baris' => $line,
                    'catatan_khusus' => $item['catatan_khusus'] ?? null,
                ];
            }

            $pajak  = round($subtotal * self::TARIF_PAJAK);
            $diskon = min(max((float) ($header['diskon_nominal'] ?? 0), 0), $subtotal);
            $grand  = $subtotal + $pajak - $diskon;

            if ($header['metode_bayar'] === 'tunai') {
                $dibayar = (float) ($header['uang_dibayar'] ?? 0);
                if ($dibayar < $grand) {
                    throw new RuntimeException('Nominal pembayaran tunai kurang dari total tagihan.');
                }
                $kembali = $dibayar - $grand;
            } else {
                $dibayar = $grand;
                $kembali = 0;
            }

            $this->insert([
                'invoice_number' => $this->generateInvoiceNumber(),
                'user_id'        => $header['user_id'],
                'tipe_pesanan'   => $header['tipe_pesanan'],
                'nomor_meja'     => $header['tipe_pesanan'] === 'dine_in' ? $header['nomor_meja'] : null,
                'nama_pemesan'   => trim((string) ($header['nama_pemesan'] ?? '')) ?: null,
                'total_subtotal' => $subtotal,
                'pajak_nominal'  => $pajak,
                'diskon_nominal' => $diskon,
                'grand_total'    => $grand,
                'metode_bayar'   => $header['metode_bayar'],
                'uang_dibayar'   => $dibayar,
                'uang_kembali'   => $kembali,
                'status'         => $header['status'] ?? 'diproses',
            ]);
            $orderId = $this->getInsertID();

            foreach ($details as &$row) {
                $row['order_id'] = $orderId;
            }
            unset($row);

            model(OrderDetailModel::class)->insertBatch($details);

            $this->db->transCommit();

            return (int) $orderId;
        } catch (Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }
    }

    public function ubahStatus(int $id, string $status): void
    {
        $order = $this->find($id);

        if ($order === null) {
            throw new RuntimeException('Pesanan tidak ditemukan.');
        }
        if ($order['status'] === 'dibatalkan') {
            throw new RuntimeException('Pesanan yang sudah dibatalkan tidak dapat diubah.');
        }

        $this->db->transBegin();

        try {
            if ($status === 'dibatalkan') {
                $productModel = model(ProductModel::class);
                foreach (model(OrderDetailModel::class)->where('order_id', $id)->findAll() as $d) {
                    $productModel->tambahStok((int) $d['product_id'], (int) $d['kuantiti']);
                }
            }

            $this->update($id, ['status' => $status]);
            $this->db->transCommit();
        } catch (Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }
    }

    public function getDetail(int $id): ?array
    {
        return $this->select('orders.*, users.nama_lengkap AS kasir')
            ->join('users', 'users.id = orders.user_id')
            ->where('orders.id', $id)
            ->first();
    }

    public function getRiwayat(?int $userId = null): array
    {
        $this->select('orders.*, users.nama_lengkap AS kasir')
            ->join('users', 'users.id = orders.user_id');

        if ($userId) {
            $this->where('orders.user_id', $userId);
        }

        return $this->orderBy('orders.created_at', 'DESC')->findAll();
    }

    public function omsetHariIni(): float
    {
        $row = $this->selectSum('grand_total', 'total')
            ->where('status', 'selesai')
            ->where('created_at >=', date('Y-m-d 00:00:00'))
            ->where('created_at <=', date('Y-m-d 23:59:59'))
            ->first();

        return (float) ($row['total'] ?? 0);
    }

    public function jumlahPesananHariIni(): int
    {
        return $this->where('status', 'selesai')
            ->where('created_at >=', date('Y-m-d 00:00:00'))
            ->where('created_at <=', date('Y-m-d 23:59:59'))
            ->countAllResults();
    }

    public function tren7Hari(): array
    {
        $rows = $this->select('DATE(created_at) AS tanggal, SUM(grand_total) AS omset', false)
            ->where('status', 'selesai')
            ->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-6 days')))
            ->groupBy('DATE(created_at)')
            ->findAll();

        $map = array_column($rows, 'omset', 'tanggal');
        $out = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl   = date('Y-m-d', strtotime("-{$i} days"));
            $out[] = ['tanggal' => $tgl, 'omset' => (float) ($map[$tgl] ?? 0)];
        }

        return $out;
    }

    public function transaksiTerbaru(int $limit = 10): array
    {
        return $this->select('orders.*, users.nama_lengkap AS kasir')
            ->join('users', 'users.id = orders.user_id')
            ->orderBy('orders.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function laporan(array $filter = []): array
    {
        $this->select(
            'orders.*, users.nama_lengkap AS kasir, '
                . '(SELECT COALESCE(SUM(kuantiti), 0) FROM order_details WHERE order_details.order_id = orders.id) AS item_terjual',
            false
        )->join('users', 'users.id = orders.user_id');

        $this->terapkanFilter($filter);

        return $this->orderBy('orders.created_at', 'DESC')->findAll();
    }

    public function ringkasan(array $filter = []): array
    {
        $this->select(
            'COALESCE(SUM(total_subtotal), 0) AS bruto, '
                . 'COALESCE(SUM(diskon_nominal), 0) AS diskon, '
                . 'COALESCE(SUM(pajak_nominal), 0) AS pajak, '
                . 'COALESCE(AVG(grand_total), 0) AS rata_rata',
            false
        )->where('orders.status', 'selesai');

        $this->terapkanFilter($filter);

        $r = $this->first();

        return [
            'bruto'     => (float) $r['bruto'],
            'diskon'    => (float) $r['diskon'],
            'pajak'     => (float) $r['pajak'],
            'bersih'    => (float) $r['bruto'] - (float) $r['diskon'],
            'rata_rata' => (float) $r['rata_rata'],
        ];
    }

    private function terapkanFilter(array $f): void
    {
        if (! empty($f['dari'])) {
            $this->where('orders.created_at >=', $f['dari'] . ' 00:00:00');
        }
        if (! empty($f['sampai'])) {
            $this->where('orders.created_at <=', $f['sampai'] . ' 23:59:59');
        }
        if (! empty($f['metode_bayar'])) {
            $this->where('orders.metode_bayar', $f['metode_bayar']);
        }
        if (! empty($f['user_id'])) {
            $this->where('orders.user_id', (int) $f['user_id']);
        }
        if (! empty($f['category_id'])) {
            $cat = (int) $f['category_id'];
            $this->where(
                "EXISTS (SELECT 1 FROM order_details od JOIN products p ON p.id = od.product_id "
                    . "WHERE od.order_id = orders.id AND p.category_id = {$cat})",
                null,
                false
            );
        }
    }
}
