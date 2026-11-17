<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h4 mb-0">Dashboard</h1>
        <div class="text-muted small"><?= date('d/m/Y') ?></div>
    </div>
    <a href="<?= site_url('transaksi/baru') ?>" class="btn btn-kopi"><i class="bi bi-plus-circle me-1"></i>Buat Transaksi Baru</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex gap-3 align-items-center">
                <div class="kpi-ikon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="text-muted small">Total Omset Hari Ini</div>
                    <div class="h5 mb-0"><?= rupiah($omsetHariIni) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex gap-3 align-items-center">
                <div class="kpi-ikon bg-primary-subtle text-primary"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="text-muted small">Jumlah Pesanan Selesai</div>
                    <div class="h5 mb-0"><?= (int) $jumlahPesanan ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex gap-3 align-items-center">
                <div class="kpi-ikon bg-warning-subtle text-warning-emphasis"><i class="bi bi-trophy"></i></div>
                <div>
                    <div class="text-muted small">Menu Terlaris (bulan ini)</div>
                    <?php if ($menuTerlaris): ?>
                        <div class="h6 mb-0"><?= esc($menuTerlaris['nama_produk']) ?></div>
                        <div class="small text-muted"><?= (int) $menuTerlaris['total_terjual'] ?> terjual</div>
                    <?php else: ?>
                        <div class="h6 mb-0">-</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex gap-3 align-items-center">
                <div class="kpi-ikon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">Peringatan Stok Rendah</div>
                    <div class="h5 mb-0"><?= count($stokRendah) ?> item</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">Omset 7 Hari Terakhir</h2>
                <canvas id="grafikTren" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">Stok Hampir Habis</h2>
                <?php if ($stokRendah): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach (array_slice($stokRendah, 0, 6) as $p): ?>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span><?= esc($p['nama_produk']) ?></span>
                                <span class="badge bg-danger"><?= (int) $p['stok'] ?> <?= esc($p['unit']) ?></span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                    <a href="<?= site_url('produk') ?>" class="small">Kelola stok</a>
                <?php else: ?>
                    <div class="text-muted small">Semua stok aman.</div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <h2 class="h6 mb-0">Transaksi Terbaru</h2>
            <a href="<?= site_url('transaksi') ?>" class="small">Lihat semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Faktur</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th>Tipe</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($terbaru as $o): ?>
                        <tr>
                            <td><a href="<?= site_url('transaksi/' . $o['id']) ?>"><?= esc($o['invoice_number']) ?></a></td>
                            <td><?= tgl($o['created_at']) ?></td>
                            <td><?= esc($o['kasir']) ?></td>
                            <td><?= label_tipe($o['tipe_pesanan']) ?></td>
                            <td class="text-end"><?= rupiah($o['grand_total']) ?></td>
                            <td><?= badge_status($o['status']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (! $terbaru): ?><tr>
                            <td colspan="6" class="text-center text-muted">Belum ada transaksi.</td>
                        </tr><?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('grafikTren'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(static fn($t) => date('d/m', strtotime($t['tanggal'])), $tren)) ?>,
            datasets: [{
                label: 'Omset',
                data: <?= json_encode(array_column($tren, 'omset')) ?>,
                backgroundColor: '#8b5a3c',
                borderRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>