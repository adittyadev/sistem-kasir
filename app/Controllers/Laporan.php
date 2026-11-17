<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\OrderModel;
use Dompdf\Dompdf;

class Laporan extends BaseController
{
    public function index()
    {
        $filter = $this->filter();
        $order  = model(OrderModel::class);

        return view('laporan/index', [
            'filter'    => $filter,
            'rows'      => $order->laporan($filter),
            'ringkasan' => $order->ringkasan($filter),
            'kategori'  => model(CategoryModel::class)->getAktif(),
        ]);
    }

    public function csv()
    {
        $rows = model(OrderModel::class)->laporan($this->filter());

        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, ['No. Faktur', 'Tanggal & Jam', 'Kasir', 'Tipe Pesanan', 'Item Terjual', 'Subtotal', 'Pajak', 'Grand Total', 'Status'], ',', '"', '');

        foreach ($rows as $r) {
            fputcsv($fh, [
                $r['invoice_number'],
                $r['created_at'],
                $r['kasir'],
                $r['tipe_pesanan'],
                $r['item_terjual'],
                $r['total_subtotal'],
                $r['pajak_nominal'],
                $r['grand_total'],
                $r['status'],
            ], ',', '"', '');
        }

        rewind($fh);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($fh);
        fclose($fh);

        return $this->response->download('laporan-penjualan-' . date('Ymd-His') . '.csv', $csv);
    }

    public function pdf()
    {
        $filter = $this->filter();
        $order  = model(OrderModel::class);

        $html = view('laporan/pdf', [
            'filter'    => $filter,
            'rows'      => $order->laporan($filter),
            'ringkasan' => $order->ringkasan($filter),
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $this->response->download('laporan-penjualan-' . date('Ymd-His') . '.pdf', $dompdf->output());
    }

    private function filter(): array
    {
        $get = $this->request->getGet();

        return [
            'dari'         => $this->tanggal($get['dari'] ?? date('Y-m-01')),
            'sampai'       => $this->tanggal($get['sampai'] ?? date('Y-m-d')),
            'category_id'  => (int) ($get['category_id'] ?? 0) ?: null,
            'metode_bayar' => in_array($get['metode_bayar'] ?? '', ['tunai', 'qris', 'transfer', 'debit'], true)
                ? $get['metode_bayar']
                : null,
        ];
    }

    private function tanggal(?string $nilai): ?string
    {
        $d = \DateTime::createFromFormat('Y-m-d', (string) $nilai);

        return $d && $d->format('Y-m-d') === $nilai ? $nilai : null;
    }
}
