<?php
$diriSendiri = $user && (int) $user['id'] === (int) session('user_id');
$role   = old('role', $user['role'] ?? 'kasir');
$status = old('status', $user['status'] ?? 'aktif');
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= $user ? 'Edit' : 'Tambah' ?> Pengguna<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="h4 mb-3"><?= $user ? 'Edit' : 'Tambah' ?> Pengguna</h1>
<div class="card" style="max-width:560px">
    <div class="card-body">
        <form method="post" action="<?= site_url($user ? "user/{$user['id']}/update" : 'user/simpan') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-12"><label class="form-label">Nama Lengkap</label>
                <input class="form-control" name="nama_lengkap" maxlength="100" required value="<?= esc(old('nama_lengkap', $user['nama_lengkap'] ?? '')) ?>">
            </div>
            <div class="col-md-6"><label class="form-label">Username</label>
                <input class="form-control" name="username" maxlength="50" required value="<?= esc(old('username', $user['username'] ?? '')) ?>">
            </div>
            <div class="col-md-6"><label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" minlength="6" autocomplete="new-password" <?= $user ? '' : 'required' ?>>
                <?php if ($user): ?><div class="form-text">Kosongkan jika tidak diubah.</div><?php endif ?>
            </div>
            <div class="col-md-6"><label class="form-label">Role</label>
                <select class="form-select" name="role" <?= $diriSendiri ? 'disabled' : '' ?>>
                    <option value="kasir" <?= $role === 'kasir' ? 'selected' : '' ?>>Kasir / Waiter</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrator / Manager</option>
                </select>
            </div>
            <div class="col-md-6"><label class="form-label">Status</label>
                <select class="form-select" name="status" <?= $diriSendiri ? 'disabled' : '' ?>>
                    <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <?php if ($diriSendiri): ?><div class="col-12 form-text">Role dan status akun Anda sendiri tidak dapat diubah.</div><?php endif ?>
            <div class="col-12">
                <button class="btn btn-kopi">Simpan</button>
                <a href="<?= site_url('user') ?>" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>