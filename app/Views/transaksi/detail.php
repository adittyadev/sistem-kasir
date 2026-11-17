<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= esc($order['invoice_number']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h4 mb-0"><?= esc($order['invoice_number']) ?></h1>
        <div class="text-muted small"><?= tgl($order['created_at']) ?> · Kasir: <?= esc($order['kasir']) ?></div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="btn-group">
            <a class="btn btn-outline-secondary" target="_blank" href="<?= site_url("transaksi/{$order['id']}/struk?ukuran=80") ?>"><i class="bi bi-printer me-1"></i>Struk 80mm</a>
            <a class="btn btn-outline-secondary" target="_blank" href="<?= site_url("transaksi/{$order['id']}/struk?ukuran=58") ?>">58mm</a>
            <a class="btn btn-outline-secondary" target="_blank" href="<?= site_url("transaksi/{$order['id']}/struk?ukuran=a4") ?>">A4</a>
        </div>
        <?php if ($order['status'] === 'diproses'): ?>
            <form method="post" action="<?= site_url("transaksi/{$order['id']}/status") ?>"><?= csrf_field() ?>
                <input type="hidden" name="status" value="selesai">
                <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>Tandai Selesai</button>
            </form>
        <?php endif ?>
        <?php if ($order['status'] !== 'dibatalkan'): ?>
            <form method="post" action="<?= site_url("transaksi/{$order['id']}/status") ?>" onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')"><?= csrf_field() ?>
                <input type="hidden" name="status" value="dibatalkan">
                <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Batalkan</button>
            </form>
        <?php endif ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detail as $d): ?>
                            <tr>
                                <td><?= esc($d['nama_produk']) ?>
                                    <?php if ($d['catatan_khusus']): ?><div class="small text-muted"><i class="bi bi-chat-left-text me-1"></i><?= esc($d['catatan_khusus']) ?></div><?php endif ?></td>
                                <td class="text-end"><?= rupiah($d['harga_satuan']) ?></td>
                                <td class="text-center"><?= (int) $d['kuantiti'] ?></td>
                                <td class="text-end"><?= rupiah($d['subtotal_baris']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 fw-normal text-muted">Status</dt>
                    <dd class="col-7 text-end"><?= badge_status($order['status']) ?></dd>
                    <dt class="col-5 fw-normal text-muted">Tipe</dt>
                    <dd class="col-7 text-end"><?= label_tipe($order['tipe_pesanan']) ?><?= $order['nomor_meja'] ? ' · Meja ' . esc($order['nomor_meja']) : '' ?></dd>
                    <?php if ($order['nama_pemesan']): ?><dt class="col-5 fw-normal text-muted">Pemesan</dt>
                        <dd class="col-7 text-end"><?= esc($order['nama_pemesan']) ?></dd><?php endif ?>
                    <dt class="col-5 fw-normal text-muted">Subtotal</dt>
                    <dd class="col-7 text-end"><?= rupiah($order['total_subtotal']) ?></dd>
                    <dt class="col-5 fw-normal text-muted">Pajak PB1</dt>
                    <dd class="col-7 text-end"><?= rupiah($order['pajak_nominal']) ?></dd>
                    <dt class="col-5 fw-normal text-muted">Diskon</dt>
                    <dd class="col-7 text-end text-danger">- <?= rupiah($order['diskon_nominal']) ?></dd>
                    <dt class="col-5 fw-semibold border-top pt-2">Grand Total</dt>
                    <dd class="col-7 text-end h5 border-top pt-2"><?= rupiah($order['grand_total']) ?></dd>
                    <dt class="col-5 fw-normal text-muted">Metode</dt>
                    <dd class="col-7 text-end"><?= label_metode($order['metode_bayar']) ?></dd>
                    <dt class="col-5 fw-normal text-muted">Dibayar</dt>
                    <dd class="col-7 text-end"><?= rupiah($order['uang_dibayar']) ?></dd>
                    <dt class="col-5 fw-normal text-muted">Kembali</dt>
                    <dd class="col-7 text-end"><?= rupiah($order['uang_kembali']) ?></dd>
                </dl>
            </div>
        </div>
        <a href="<?= site_url('transaksi/baru') ?>" class="btn btn-kopi w-100 mt-3"><i class="bi bi-plus-circle me-1"></i>Transaksi Baru</a>
    </div>
</div>
<?= $this->endSection() ?>