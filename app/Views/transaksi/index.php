<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Riwayat Transaksi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Riwayat Transaksi<?= session('role') === 'kasir' ? ' Saya' : '' ?></h1>
    <a href="<?= site_url('transaksi/baru') ?>" class="btn btn-kopi"><i class="bi bi-plus-circle me-1"></i>Transaksi Baru</a>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Faktur</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Tipe</th>
                    <th>Bayar</th>
                    <th class="text-end">Grand Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><a href="<?= site_url('transaksi/' . $o['id']) ?>"><?= esc($o['invoice_number']) ?></a></td>
                        <td><?= tgl($o['created_at']) ?></td>
                        <td><?= esc($o['kasir']) ?></td>
                        <td><?= label_tipe($o['tipe_pesanan']) ?><?= $o['nomor_meja'] ? ' · Meja ' . esc($o['nomor_meja']) : '' ?></td>
                        <td><?= label_metode($o['metode_bayar']) ?></td>
                        <td class="text-end"><?= rupiah($o['grand_total']) ?></td>
                        <td><?= badge_status($o['status']) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $orders): ?><tr>
                        <td colspan="7" class="text-center text-muted">Belum ada transaksi.</td>
                    </tr><?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>