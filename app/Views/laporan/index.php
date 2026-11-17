<?php
$qs = http_build_query(array_map(static fn($v) => $v ?? '', $filter));
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Laporan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h4 mb-0">Laporan Penjualan</h1>
    <div class="btn-group">
        <a class="btn btn-outline-danger" href="<?= site_url('laporan/pdf?' . $qs) ?>"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a class="btn btn-outline-success" href="<?= site_url('laporan/csv?' . $qs) ?>"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel/CSV</a>
    </div>
</div>

<form method="get" class="card mb-3">
    <div class="card-body row g-2 align-items-end">
        <div class="col-6 col-md-2"><label class="form-label small mb-1">Dari Tanggal</label><input type="date" name="dari" class="form-control" value="<?= esc($filter['dari']) ?>"></div>
        <div class="col-6 col-md-2"><label class="form-label small mb-1">Sampai Tanggal</label><input type="date" name="sampai" class="form-control" value="<?= esc($filter['sampai']) ?>"></div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Kategori Menu</label>
            <select name="category_id" class="form-select">
                <option value="">Semua</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= (int) $filter['category_id'] === (int) $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Metode Pembayaran</label>
            <select name="metode_bayar" class="form-select">
                <option value="">Semua</option>
                <?php foreach (['tunai', 'qris', 'transfer', 'debit'] as $m): ?>
                    <option value="<?= $m ?>" <?= $filter['metode_bayar'] === $m ? 'selected' : '' ?>><?= label_metode($m) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-kopi w-100"><i class="bi bi-funnel me-1"></i>Terapkan</button></div>
    </div>
</form>

<div class="row g-3 mb-3">
    <?php foreach (
        [
            'Omset Bruto'        => $ringkasan['bruto'],
            'Total Diskon'       => $ringkasan['diskon'],
            'Pajak Terkumpul'    => $ringkasan['pajak'],
            'Omset Bersih'       => $ringkasan['bersih'],
            'Rata-rata Transaksi' => $ringkasan['rata_rata'],
        ] as $label => $nilai
    ): ?>
        <div class="col-6 col-lg">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="text-muted small"><?= $label ?></div>
                    <div class="h6 mb-0"><?= rupiah($nilai) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<div class="small text-muted mb-2">Ringkasan hanya menghitung pesanan berstatus selesai. Omset bersih = bruto - diskon.</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal &amp; Jam</th>
                    <th>Kasir</th>
                    <th>Tipe</th>
                    <th class="text-end">Item</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Pajak</th>
                    <th class="text-end">Grand Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><a href="<?= site_url('transaksi/' . $r['id']) ?>"><?= esc($r['invoice_number']) ?></a></td>
                        <td><?= tgl($r['created_at']) ?></td>
                        <td><?= esc($r['kasir']) ?></td>
                        <td><?= label_tipe($r['tipe_pesanan']) ?></td>
                        <td class="text-end"><?= (int) $r['item_terjual'] ?></td>
                        <td class="text-end"><?= rupiah($r['total_subtotal']) ?></td>
                        <td class="text-end"><?= rupiah($r['pajak_nominal']) ?></td>
                        <td class="text-end"><?= rupiah($r['grand_total']) ?></td>
                        <td><?= badge_status($r['status']) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $rows): ?><tr>
                        <td colspan="9" class="text-center text-muted">Tidak ada data pada periode ini.</td>
                    </tr><?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>