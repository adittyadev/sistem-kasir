<?php $v = static fn(string $f, $default = '') => esc(old($f, $produk[$f] ?? $default)); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= $produk ? 'Edit' : 'Tambah' ?> Produk<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="h4 mb-3"><?= $produk ? 'Edit' : 'Tambah' ?> Produk</h1>
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="post" action="<?= site_url($produk ? "produk/{$produk['id']}/update" : 'produk/simpan') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select class="form-select" name="category_id" required>
                    <option value="">- Pilih -</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (int) old('category_id', $produk['category_id'] ?? 0) === (int) $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Kode Produk</label><input class="form-control" name="kode_produk" maxlength="20" required value="<?= $v('kode_produk') ?>"></div>
            <div class="col-12"><label class="form-label">Nama Produk</label><input class="form-control" name="nama_produk" maxlength="100" required value="<?= $v('nama_produk') ?>"></div>
            <div class="col-md-6"><label class="form-label">Harga Jual (Rp)</label><input class="form-control" type="number" min="0" step="any" name="harga_jual" required value="<?= $v('harga_jual') ?>"></div>
            <div class="col-md-6"><label class="form-label">Harga Pokok (Rp)</label><input class="form-control" type="number" min="0" step="any" name="harga_pokok" required value="<?= $v('harga_pokok') ?>"></div>
            <div class="col-md-6"><label class="form-label">Stok</label><input class="form-control" type="number" min="0" name="stok" required value="<?= $v('stok', 0) ?>"></div>
            <div class="col-md-6"><label class="form-label">Unit</label><input class="form-control" name="unit" maxlength="20" required value="<?= $v('unit', 'porsi') ?>" placeholder="cup / porsi / pcs"></div>
            <div class="col-12">
                <button class="btn btn-kopi">Simpan</button>
                <a href="<?= site_url('produk') ?>" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>