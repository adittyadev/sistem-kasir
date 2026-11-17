<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Kategori<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Kategori Menu</h1>
    <a href="<?= site_url('kategori/tambah') ?>" class="btn btn-kopi"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategori as $i => $k): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($k['nama_kategori']) ?></td>
                        <td class="text-muted"><?= esc($k['slug']) ?></td>
                        <td><span class="badge bg-<?= $k['status'] === 'aktif' ? 'success' : 'secondary' ?>"><?= esc(ucfirst($k['status'])) ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url("kategori/{$k['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form action="<?= site_url("kategori/{$k['id']}/hapus") ?>" method="post" class="d-inline"
                                data-judul="Hapus kategori?"
                                data-konfirmasi="Kategori <?= esc($k['nama_kategori']) ?> akan dihapus dan tidak bisa dikembalikan."
                                data-tombol="Ya, hapus">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" aria-label="Hapus <?= esc($k['nama_kategori']) ?>"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $kategori): ?><tr>
                        <td colspan="5" class="text-center text-muted">Belum ada kategori.</td>
                    </tr><?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>