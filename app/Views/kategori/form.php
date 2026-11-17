<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= $kategori ? 'Edit' : 'Tambah' ?> Kategori<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="h4 mb-3"><?= $kategori ? 'Edit' : 'Tambah' ?> Kategori</h1>
<div class="card">
    <div class="card-body">
        <form method="post" action="<?= site_url($kategori ? "kategori/{$kategori['id']}/update" : 'kategori/simpan') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-8">
                <label class="form-label" for="namaKategori">Nama Kategori</label>
                <input class="form-control" id="namaKategori" name="nama_kategori" maxlength="50" required autofocus
                    value="<?= esc(old('nama_kategori', $kategori['nama_kategori'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <?php $status = old('status', $kategori['status'] ?? 'aktif'); ?>
                <select class="form-select" id="status" name="status">
                    <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-kopi">Simpan</button>
                <a href="<?= site_url('kategori') ?>" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>