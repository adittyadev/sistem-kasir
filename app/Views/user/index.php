<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Pengguna<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Akun Pengguna</h1>
    <a href="<?= site_url('user/tambah') ?>" class="btn btn-kopi"><i class="bi bi-person-plus me-1"></i>Tambah</a>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= esc($u['username']) ?></td>
                        <td><?= esc($u['nama_lengkap']) ?></td>
                        <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'dark' : 'info' ?>"><?= esc(ucfirst($u['role'])) ?></span></td>
                        <td><span class="badge bg-<?= $u['status'] === 'aktif' ? 'success' : 'secondary' ?>"><?= esc(ucfirst($u['status'])) ?></span></td>
                        <td><?= tgl($u['created_at'], 'd/m/Y') ?></td>
                        <td class="text-end"><a href="<?= site_url("user/{$u['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>