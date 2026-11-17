<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h1 {
            font-size: 16px;
            margin: 0;
        }

        .sub {
            color: #666;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 4px 6px;
        }

        th {
            background: #4b2e1e;
            color: #fff;
            text-align: left;
        }

        .kanan {
            text-align: right;
        }

        .ringkas td {
            border: 0;
            padding: 2px 8px 2px 0;
        }

        .ringkas {
            width: auto;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h1>Laporan Penjualan - Titik Temu Kopi</h1>
    <div class="sub">
        Periode: <?= $filter['dari'] ? tgl($filter['dari'], 'd/m/Y') : 'awal' ?> s/d <?= $filter['sampai'] ? tgl($filter['sampai'], 'd/m/Y') : 'sekarang' ?>
        <?= $filter['metode_bayar'] ? ' | Metode: ' . label_metode($filter['metode_bayar']) : '' ?>
        | Dicetak: <?= date('d/m/Y H:i') ?>
    </div>

    <table class="ringkas">
        <tr>
            <td>Omset Bruto</td>
            <td><b><?= rupiah($ringkasan['bruto']) ?></b></td>
            <td>Total Diskon</td>
            <td><b><?= rupiah($ringkasan['diskon']) ?></b></td>
            <td>Pajak Terkumpul</td>
            <td><b><?= rupiah($ringkasan['pajak']) ?></b></td>
        </tr>
        <tr>
            <td>Omset Bersih</td>
            <td><b><?= rupiah($ringkasan['bersih']) ?></b></td>
            <td>Rata-rata Transaksi</td>
            <td><b><?= rupiah($ringkasan['rata_rata']) ?></b></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No. Faktur</th>
                <th>Tanggal &amp; Jam</th>
                <th>Kasir</th>
                <th>Tipe</th>
                <th class="kanan">Item</th>
                <th class="kanan">Subtotal</th>
                <th class="kanan">Pajak</th>
                <th class="kanan">Grand Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= esc($r['invoice_number']) ?></td>
                    <td><?= tgl($r['created_at']) ?></td>
                    <td><?= esc($r['kasir']) ?></td>
                    <td><?= label_tipe($r['tipe_pesanan']) ?></td>
                    <td class="kanan"><?= (int) $r['item_terjual'] ?></td>
                    <td class="kanan"><?= rupiah($r['total_subtotal']) ?></td>
                    <td class="kanan"><?= rupiah($r['pajak_nominal']) ?></td>
                    <td class="kanan"><?= rupiah($r['grand_total']) ?></td>
                    <td><?= esc(ucfirst($r['status'])) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>

</html>