<?php

namespace App\Controllers;

use App\Models\OrderDetailModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $order    = model(OrderModel::class);
        $terlaris = model(OrderDetailModel::class)->menuTerlaris(date('Y-m-01'), date('Y-m-d'));
        $stokRendah = model(ProductModel::class)->getStokRendah();

        return view('dashboard/index', [
            'omsetHariIni'  => $order->omsetHariIni(),
            'jumlahPesanan' => $order->jumlahPesananHariIni(),
            'menuTerlaris'  => $terlaris[0] ?? null,
            'stokRendah'    => $stokRendah,
            'tren'          => $order->tren7Hari(),
            'terbaru'       => $order->transaksiTerbaru(10),
        ]);
    }
}
