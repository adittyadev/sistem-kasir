<?php $batas = \App\Models\ProductModel::BATAS_STOK_RENDAH; ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Produk<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Master Menu &amp; Stok</h1>
    <a href="<?= site_url('produk/tambah') ?>" class="btn btn-kopi"><i class="bi bi-plus-lg me-1"></i>Tambah Produk</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-sm-4">
        <select name="category_id" class="form-select">
            <option value="">Semua kategori</option>
            <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>" <?= (int) $categoryId === (int) $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="col-sm-5"><input name="q" class="form-control" placeholder="Cari nama / kode produk" value="<?= esc($keyword) ?>"></div>
    <div class="col-sm-3"><button class="btn btn-outline-secondary w-100"><i class="bi bi-search me-1"></i>Cari</button></div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Harga Pokok</th>
                    <th class="text-end">Stok</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produk as $p): ?>
                    <tr>
                        <td class="text-muted"><?= esc($p['kode_produk']) ?></td>
                        <td><?= esc($p['nama_produk']) ?></td>
                        <td><?= esc($p['nama_kategori']) ?></td>
                        <td class="text-end"><?= rupiah($p['harga_jual']) ?></td>
                        <td class="text-end"><?= rupiah($p['harga_pokok']) ?></td>
                        <td class="text-end">
                            <span class="badge bg-<?= $p['stok'] <= $batas ? 'danger' : 'success' ?>"><?= (int) $p['stok'] ?> <?= esc($p['unit']) ?></span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url("produk/{$p['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form action="<?= site_url("produk/{$p['id']}/hapus") ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $produk): ?><tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada produk.</td>
                    </tr><?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>