<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= $kategori ? 'Edit' : 'Tambah' ?> Kategori<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="h4 mb-3"><?= $kategori ? 'Edit' : 'Tambah' ?> Kategori</h1>
<div class="card" style="max-width:520px">
    <div class="card-body">
        <form method="post" action="<?= site_url($kategori ? "kategori/{$kategori['id']}/update" : 'kategori/simpan') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Kategori</label>
                <input class="form-control" name="nama_kategori" maxlength="50" required
                    value="<?= esc(old('nama_kategori', $kategori['nama_kategori'] ?? '')) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Status</label>
                <?php $status = old('status', $kategori['status'] ?? 'aktif'); ?>
                <select class="form-select" name="status">
                    <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <button class="btn btn-kopi">Simpan</button>
            <a href="<?= site_url('kategori') ?>" class="btn btn-light">Batal</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>