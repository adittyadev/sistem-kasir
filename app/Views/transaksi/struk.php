<?php
$a4    = $ukuran === 'a4';
$lebar = ['58' => '48mm', '80' => '72mm', 'a4' => '180mm'][$ukuran];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Struk <?= esc($order['invoice_number']) ?></title>
    <style>
        @page {
            size: <?= $a4 ? 'A4' : $ukuran . 'mm auto' ?>;
            margin: <?= $a4 ? '15mm' : '3mm' ?>;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: <?= $a4 ? '13px' : '11px' ?>;
            width: <?= $lebar ?>;
            margin: 0 auto;
            color: #000;
        }

        .tengah {
            text-align: center;
        }

        .baris {
            display: flex;
            justify-content: space-between;
            gap: 6px;
        }

        .garis {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .tebal {
            font-weight: bold;
        }

        .catatan {
            padding-left: 8px;
            font-style: italic;
        }

        .toolbar {
            padding: 8px 0;
            text-align: center;
            font-family: sans-serif;
        }

        .toolbar a,
        .toolbar button {
            margin: 0 3px;
        }

        @media print {
            .toolbar {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">Cetak</button>
        <a href="?ukuran=80">80mm</a> <a href="?ukuran=58">58mm</a> <a href="?ukuran=a4">A4</a>
    </div>

    <div class="tengah">
        <div class="tebal">TITIK TEMU KOPI</div>
        <div>Struk Pembayaran</div>
    </div>
    <div class="garis"></div>
    <div class="baris"><span>No</span><span><?= esc($order['invoice_number']) ?></span></div>
    <div class="baris"><span>Tgl</span><span><?= tgl($order['created_at']) ?></span></div>
    <div class="baris"><span>Kasir</span><span><?= esc($order['kasir']) ?></span></div>
    <div class="baris"><span>Tipe</span><span><?= label_tipe($order['tipe_pesanan']) ?><?= $order['nomor_meja'] ? ' / Meja ' . esc($order['nomor_meja']) : '' ?></span></div>
    <?php if ($order['nama_pemesan']): ?><div class="baris"><span>Pemesan</span><span><?= esc($order['nama_pemesan']) ?></span></div><?php endif ?>
    <div class="garis"></div>

    <?php foreach ($detail as $d): ?>
        <div><?= esc($d['nama_produk']) ?></div>
        <?php if ($d['catatan_khusus']): ?><div class="catatan">* <?= esc($d['catatan_khusus']) ?></div><?php endif ?>
        <div class="baris"><span><?= (int) $d['kuantiti'] ?> x <?= number_format((float) $d['harga_satuan'], 0, ',', '.') ?></span><span><?= number_format((float) $d['subtotal_baris'], 0, ',', '.') ?></span></div>
    <?php endforeach ?>

    <div class="garis"></div>
    <div class="baris"><span>Subtotal</span><span><?= rupiah($order['total_subtotal']) ?></span></div>
    <div class="baris"><span>Pajak PB1 10%</span><span><?= rupiah($order['pajak_nominal']) ?></span></div>
    <?php if ((float) $order['diskon_nominal'] > 0): ?>
        <div class="baris"><span>Diskon</span><span>-<?= rupiah($order['diskon_nominal']) ?></span></div>
    <?php endif ?>
    <div class="baris tebal"><span>TOTAL</span><span><?= rupiah($order['grand_total']) ?></span></div>
    <div class="garis"></div>
    <div class="baris"><span><?= label_metode($order['metode_bayar']) ?></span><span><?= rupiah($order['uang_dibayar']) ?></span></div>
    <div class="baris"><span>Kembali</span><span><?= rupiah($order['uang_kembali']) ?></span></div>
    <div class="garis"></div>
    <div class="tengah">Terima kasih atas kunjungan Anda</div>
    <?php if ($order['status'] === 'dibatalkan'): ?><div class="tengah tebal">** DIBATALKAN **</div><?php endif ?>
</body>

</html>