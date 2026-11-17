<?php
$oldProduk  = (array) old('product_id');
$oldQty     = (array) old('kuantiti');
$oldCatatan = (array) old('catatan_khusus');
$oldItems   = [];
foreach ($oldProduk as $i => $pid) {
    $oldItems[] = [$pid, $oldQty[$i] ?? 1, $oldCatatan[$i] ?? ''];
}
$tipe   = old('tipe_pesanan', 'dine_in');
$metode = old('metode_bayar', 'tunai');
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Transaksi Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="h4 mb-1">Transaksi Baru</h1>
<div class="text-muted small mb-3">Nomor invoice (INV-POS-YYYYMMDD-XXXX) dibuat otomatis saat transaksi disimpan.</div>

<form method="post" action="<?= site_url('transaksi/simpan') ?>" id="formTransaksi" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label d-block">Tipe Pesanan</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="tipe_pesanan" id="tipeDine" value="dine_in" <?= $tipe === 'dine_in' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="tipeDine"><i class="bi bi-shop me-1"></i>Dine-In</label>
                            <input type="radio" class="btn-check" name="tipe_pesanan" id="tipeTake" value="takeaway" <?= $tipe === 'takeaway' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="tipeTake"><i class="bi bi-bag me-1"></i>Takeaway</label>
                        </div>
                    </div>
                    <div class="col-md-5" id="wrapMeja">
                        <label class="form-label" for="nomorMeja">No. Meja</label>
                        <select class="form-select" name="nomor_meja" id="nomorMeja">
                            <option value="">- Pilih meja -</option>
                            <?php for ($m = 1; $m <= 20; $m++): ?>
                                <option value="<?= $m ?>" <?= (string) old('nomor_meja') === (string) $m ? 'selected' : '' ?>>Meja <?= $m ?></option>
                            <?php endfor ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="namaPemesan">Nama Pemesan / Catatan Meja</label>
                        <input class="form-control" type="text" name="nama_pemesan" id="namaPemesan" maxlength="100"
                            placeholder="mis. Budi, meja dekat jendela" value="<?= esc(old('nama_pemesan')) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">Item Pesanan</h2>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnTambah"><i class="bi bi-plus-lg me-1"></i>Tambah Item</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="min-width:210px">Menu</th>
                                <th style="width:90px">Qty</th>
                                <th style="min-width:160px">Catatan Khusus</th>
                                <th class="text-end">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="barisItem"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h6">Ringkasan &amp; Pembayaran</h2>
                <dl class="row mb-2 small">
                    <dt class="col-6 fw-normal text-muted">Subtotal</dt>
                    <dd class="col-6 text-end mb-1" id="tSubtotal">Rp 0</dd>
                    <dt class="col-6 fw-normal text-muted">Pajak PB1 (10%)</dt>
                    <dd class="col-6 text-end mb-1" id="tPajak">Rp 0</dd>
                    <dt class="col-6 fw-normal text-muted">Diskon</dt>
                    <dd class="col-6 text-end mb-1 text-danger" id="tDiskon">- Rp 0</dd>
                </dl>
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mb-3">
                    <span class="fw-semibold">Grand Total</span><span class="h4 mb-0" id="tGrand">Rp 0</span>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="diskon">Diskon / Voucher (Rp)</label>
                    <input class="form-control" type="number" min="0" name="diskon_nominal" id="diskon" value="<?= esc(old('diskon_nominal', 0)) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="metode">Metode Pembayaran</label>
                    <select class="form-select" name="metode_bayar" id="metode">
                        <?php foreach (['tunai', 'qris', 'transfer', 'debit'] as $m): ?>
                            <option value="<?= $m ?>" <?= $metode === $m ? 'selected' : '' ?>><?= label_metode($m) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div id="wrapTunai">
                    <label class="form-label" for="bayar">Uang Dibayar (Rp)</label>
                    <div class="input-group mb-2">
                        <input class="form-control" type="number" min="0" name="uang_dibayar" id="bayar" value="<?= esc(old('uang_dibayar')) ?>">
                        <button type="button" class="btn btn-outline-secondary" id="btnPas">Uang Pas</button>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Kembalian</span><strong id="tKembali">Rp 0</strong>
                    </div>
                    <div class="text-danger small d-none" id="pesanKurang">Uang dibayar kurang dari total tagihan.</div>
                </div>

                <button class="btn btn-kopi w-100 mt-3" type="submit"><i class="bi bi-check2-circle me-1"></i>Simpan Transaksi</button>
            </div>
        </div>
    </div>
</form>

<!-- Template baris item -->
<template id="tplBaris">
    <tr>
        <td>
            <select name="product_id[]" class="form-select form-select-sm pilih-menu" required>
                <option value="">- Pilih menu -</option>
                <?php $kategoriAktif = null; ?>
                <?php foreach ($produk as $p): ?>
                    <?php if ($kategoriAktif !== $p['nama_kategori']): ?>
                        <?= $kategoriAktif !== null ? '</optgroup>' : '' ?>
                        <optgroup label="<?= esc($p['nama_kategori']) ?>">
                            <?php $kategoriAktif = $p['nama_kategori']; ?>
                        <?php endif ?>
                        <option value="<?= $p['id'] ?>" data-harga="<?= (float) $p['harga_jual'] ?>" data-stok="<?= (int) $p['stok'] ?>">
                            <?= esc($p['nama_produk']) ?> · <?= rupiah($p['harga_jual']) ?> (stok <?= (int) $p['stok'] ?>)
                        </option>
                    <?php endforeach ?>
                    <?= $kategoriAktif !== null ? '</optgroup>' : '' ?>
            </select>
        </td>
        <td><input type="number" name="kuantiti[]" class="form-control form-control-sm qty" min="1" value="1" required></td>
        <td><input type="text" name="catatan_khusus[]" class="form-control form-control-sm" maxlength="255" placeholder="Less sugar, extra ice..."></td>
        <td class="text-end text-nowrap subtotal-baris">Rp 0</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger hapus-baris" title="Hapus"><i class="bi bi-x-lg"></i></button></td>
    </tr>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const TARIF_PAJAK = 0.10;
    const oldItems = <?= json_encode($oldItems) ?>;

    const $ = id => document.getElementById(id);
    const tbody = $('barisItem'),
        tpl = $('tplBaris'),
        form = $('formTransaksi');
    const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    let grand = 0;

    function tambahBaris(pid = '', qty = 1, catatan = '') {
        const tr = tpl.content.firstElementChild.cloneNode(true);
        tr.querySelector('select').value = pid;
        tr.querySelector('.qty').value = qty;
        tr.querySelector('[name="catatan_khusus[]"]').value = catatan;
        tbody.appendChild(tr);
        hitung();
    }

    function hitung() {
        let subtotal = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const opt = tr.querySelector('select').selectedOptions[0];
            const qtyEl = tr.querySelector('.qty');
            const harga = opt && opt.value ? Number(opt.dataset.harga) : 0;
            if (opt && opt.value) qtyEl.max = opt.dataset.stok;
            const baris = harga * (Number(qtyEl.value) || 0);
            tr.querySelector('.subtotal-baris').textContent = fmt(baris);
            subtotal += baris;
        });

        const pajak = Math.round(subtotal * TARIF_PAJAK);
        const diskon = Math.min(Math.max(Number($('diskon').value) || 0, 0), subtotal);
        grand = subtotal + pajak - diskon;

        $('tSubtotal').textContent = fmt(subtotal);
        $('tPajak').textContent = fmt(pajak);
        $('tDiskon').textContent = '- ' + fmt(diskon);
        $('tGrand').textContent = fmt(grand);

        const kembali = (Number($('bayar').value) || 0) - grand;
        $('tKembali').textContent = fmt(Math.max(kembali, 0));
        $('pesanKurang').classList.toggle('d-none', !(isTunai() && $('bayar').value !== '' && kembali < 0));
    }

    const isTunai = () => $('metode').value === 'tunai';
    const isDineIn = () => document.querySelector('[name="tipe_pesanan"]:checked').value === 'dine_in';

    function aturTampilan() {
        $('wrapMeja').classList.toggle('d-none', !isDineIn());
        $('nomorMeja').required = isDineIn();
        $('wrapTunai').classList.toggle('d-none', !isTunai());
        $('bayar').disabled = !isTunai();
        hitung();
    }

    tbody.addEventListener('input', hitung);
    tbody.addEventListener('change', hitung);
    tbody.addEventListener('click', e => {
        const tombol = e.target.closest('.hapus-baris');
        if (!tombol) return;
        if (tbody.rows.length > 1) {
            tombol.closest('tr').remove();
            hitung();
        }
    });
    $('btnTambah').addEventListener('click', () => tambahBaris());
    $('btnPas').addEventListener('click', () => {
        $('bayar').value = grand;
        hitung();
    });
    ['diskon', 'bayar'].forEach(id => $(id).addEventListener('input', hitung));
    $('metode').addEventListener('change', aturTampilan);
    document.querySelectorAll('[name="tipe_pesanan"]').forEach(r => r.addEventListener('change', aturTampilan));

    form.addEventListener('submit', e => {
        if (grand <= 0) {
            e.preventDefault();
            alert('Pilih minimal satu menu.');
            return;
        }
        if (isTunai() && (Number($('bayar').value) || 0) < grand) {
            e.preventDefault();
            alert('Uang dibayar kurang dari total tagihan.');
        }
    });

    (oldItems.length ? oldItems : [
        ['', 1, '']
    ]).forEach(([p, q, c]) => tambahBaris(p, q, c));
    aturTampilan();
</script>
<?= $this->endSection() ?>