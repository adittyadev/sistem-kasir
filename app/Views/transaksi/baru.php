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

$jumlahMeja = 20;
$mejaTerisi = array_map('intval', (array) ($mejaTerisi ?? []));
$kapasitas  = static fn(int $m): int => $m <= 6 ? 2 : ($m <= 14 ? 4 : 6);
$oldMeja    = (string) old('nomor_meja');

$petaProduk = [];
foreach ($produk as $p) {
    $petaProduk[$p['id']] = [
        'nama'     => $p['nama_produk'],
        'harga'    => (float) $p['harga_jual'],
        'stok'     => (int) $p['stok'],
        'kategori' => $p['nama_kategori'],
    ];
}
$daftarKategori = array_values(array_unique(array_column($produk, 'nama_kategori')));
$jsonFlag       = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Transaksi Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .legenda {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem 1.25rem;
        font-size: .85rem;
        color: #6d5f57;
    }

    .legenda i {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 4px;
        margin-right: .4rem;
        vertical-align: -2px;
    }

    .meja {
        --warna-kursi: #7fb08a;
        --sandaran: #5b8f68;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        width: 100%;
        padding: .8rem .5rem .6rem;
        color: inherit;
        background: #fff;
        border: 1.5px solid #e4dcd4;
        border-radius: .9rem;
        transition: border-color .15s, box-shadow .15s, transform .15s, background .15s;
    }

    .meja:hover:not(:disabled) {
        border-color: var(--kopi);
        box-shadow: 0 4px 12px rgba(42, 26, 18, .12);
        transform: translateY(-2px);
    }

    .meja:focus-visible {
        outline: 3px solid rgba(139, 90, 60, .4);
        outline-offset: 2px;
    }

    .baris-kursi {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .kursi {
        width: 26px;
        height: 18px;
        background: var(--warna-kursi);
        transition: transform .18s ease, background .15s;
    }

    .baris-kursi.atas .kursi {
        border-top: 5px solid var(--sandaran);
        border-radius: 9px 9px 4px 4px;
    }

    .baris-kursi.bawah .kursi {
        border-bottom: 5px solid var(--sandaran);
        border-radius: 4px 4px 9px 9px;
    }

    .meja:hover:not(:disabled) .baris-kursi.atas .kursi {
        transform: translateY(-3px);
    }

    .meja:hover:not(:disabled) .baris-kursi.bawah .kursi {
        transform: translateY(3px);
    }

    .papan {
        width: var(--lebar, 100%);
        max-width: 100%;
        min-height: 54px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.15;
        background: #fff;
        border: 1.5px solid #d9cfc6;
        border-radius: .6rem;
    }

    .papan .no {
        font-weight: 700;
        font-size: 1.15rem;
    }

    .papan small {
        color: #8a7d74;
        font-size: .7rem;
    }

    .meja .status {
        font-size: .72rem;
        font-weight: 600;
        color: #3f7550;
    }

    .meja.dipilih {
        --warna-kursi: #8b5a3c;
        --sandaran: #5f3a24;
        background: #fbf3ec;
        border-color: var(--kopi);
        box-shadow: 0 0 0 3px rgba(139, 90, 60, .2);
    }

    .meja.dipilih .papan {
        border-color: var(--kopi);
    }

    .meja.dipilih .status {
        color: var(--kopi);
    }

    .meja.dipilih .kursi {
        animation: duduk .35s ease both;
    }

    .meja.dipilih .kursi:nth-child(2) {
        animation-delay: .06s;
    }

    .meja.dipilih .kursi:nth-child(3) {
        animation-delay: .12s;
    }

    @keyframes duduk {
        0% {
            transform: scale(.6);
        }

        60% {
            transform: scale(1.15);
        }

        100% {
            transform: scale(1);
        }
    }

    .meja:disabled {
        --warna-kursi: #d4c9c1;
        --sandaran: #b9aca3;
        background: #f4f0ec;
        cursor: not-allowed;
    }

    .meja:disabled .papan {
        background: repeating-linear-gradient(45deg, #ece6e0, #ece6e0 6px, #f4f0ec 6px, #f4f0ec 12px);
    }

    .meja:disabled .status {
        color: #a0392b;
    }

    /* ===== Pilih menu ===== */
    .pil-kategori {
        white-space: nowrap;
    }

    .pil-kategori.active {
        background: var(--kopi);
        border-color: var(--kopi);
        color: #fff;
    }

    .kartu-menu {
        display: block;
        width: 100%;
        height: 100%;
        text-align: left;
        color: inherit;
        padding: .7rem .8rem;
        background: #fff;
        border: 1.5px solid #e4dcd4;
        border-radius: .75rem;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }

    .kartu-menu:hover:not(:disabled) {
        border-color: var(--kopi);
        box-shadow: 0 3px 10px rgba(42, 26, 18, .1);
    }

    .kartu-menu:focus-visible {
        outline: 3px solid rgba(139, 90, 60, .4);
        outline-offset: 2px;
    }

    .kartu-menu.dipilih {
        border-color: var(--kopi);
        background: #fbf3ec;
    }

    .kartu-menu:disabled {
        background: #f4f0ec;
        color: #9b8f86;
        cursor: not-allowed;
    }

    @media (prefers-reduced-motion: reduce) {

        .meja,
        .kursi,
        .kartu-menu {
            transition: none;
        }

        .meja.dipilih .kursi {
            animation: none;
        }

        .meja:hover:not(:disabled) .kursi {
            transform: none;
        }
    }
</style>

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
                        <label class="form-label" for="btnPilihMeja">No. Meja</label>
                        <input type="hidden" name="nomor_meja" id="nomorMeja" value="">
                        <button type="button" class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center"
                            id="btnPilihMeja" data-bs-toggle="modal" data-bs-target="#modalMeja">
                            <span id="labelMeja" class="text-muted">Pilih meja</span>
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <div class="text-danger small mt-1 d-none" id="pesanMeja">Pilih meja terlebih dahulu.</div>
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

<div class="modal fade" id="modalMeja" tabindex="-1" aria-labelledby="judulModalMeja" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="judulModalMeja">Pilih Meja</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="px-3 pt-3 pb-2 border-bottom">
                <div class="legenda">
                    <span><i style="background:#7fb08a"></i>Tersedia</span>
                    <span><i style="background:#8b5a3c"></i>Dipilih</span>
                    <span><i style="background:#d4c9c1"></i>Terisi</span>
                </div>
            </div>
            <div class="modal-body">
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    <?php for ($m = 1; $m <= $jumlahMeja; $m++): ?>
                        <?php
                        $kap      = $kapasitas($m);
                        $perBaris = intdiv($kap, 2);
                        $terisi   = in_array($m, $mejaTerisi, true);
                        $lebar    = $perBaris * 32 + 24;
                        ?>
                        <div class="col">
                            <button type="button" class="meja" data-no="<?= $m ?>" data-kursi="<?= $kap ?>"
                                style="--lebar: <?= $lebar ?>px" <?= $terisi ? 'disabled' : '' ?>
                                aria-label="Meja <?= $m ?>, <?= $kap ?> kursi, <?= $terisi ? 'terisi' : 'tersedia' ?>">
                                <span class="baris-kursi atas" aria-hidden="true">
                                    <?php for ($k = 0; $k < $perBaris; $k++): ?><span class="kursi"></span><?php endfor ?>
                                </span>
                                <span class="papan">
                                    <span class="no"><?= $m ?></span>
                                    <small><?= $kap ?> kursi</small>
                                </span>
                                <span class="baris-kursi bawah" aria-hidden="true">
                                    <?php for ($k = 0; $k < $perBaris; $k++): ?><span class="kursi"></span><?php endfor ?>
                                </span>
                                <span class="status"><?= $terisi ? 'Terisi' : 'Tersedia' ?></span>
                            </button>
                        </div>
                    <?php endfor ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMenu" tabindex="-1" aria-labelledby="judulModalMenu" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="judulModalMenu">Pilih Menu</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="px-3 pt-3 pb-3 border-bottom">
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" id="cariMenu" placeholder="Cari nama menu" autocomplete="off">
                </div>
                <div class="d-flex gap-2 overflow-auto pb-1" id="pilKategori">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill pil-kategori active" data-kategori="">Semua</button>
                    <?php foreach ($daftarKategori as $kat): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill pil-kategori" data-kategori="<?= esc($kat) ?>"><?= esc($kat) ?></button>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="modal-body">
                <div class="row g-2" id="daftarMenu">
                    <?php foreach ($produk as $p): ?>
                        <?php $stok = (int) $p['stok']; ?>
                        <div class="col-6 col-md-4 item-menu" data-kategori="<?= esc($p['nama_kategori']) ?>" data-cari="<?= esc(mb_strtolower($p['nama_produk'])) ?>">
                            <button type="button" class="kartu-menu" data-id="<?= $p['id'] ?>" <?= $stok <= 0 ? 'disabled' : '' ?>>
                                <div class="fw-medium"><?= esc($p['nama_produk']) ?></div>
                                <div class="text-muted small"><?= esc($p['nama_kategori']) ?></div>
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    <span class="fw-semibold"><?= rupiah($p['harga_jual']) ?></span>
                                    <?php if ($stok <= 0): ?>
                                        <span class="badge text-bg-secondary">Habis</span>
                                    <?php elseif ($stok <= 5): ?>
                                        <span class="badge text-bg-warning">Stok <?= $stok ?></span>
                                    <?php else: ?>
                                        <span class="small text-muted">Stok <?= $stok ?></span>
                                    <?php endif ?>
                                </div>
                            </button>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="text-muted text-center py-4 d-none" id="menuKosong">Menu tidak ditemukan.</div>
            </div>
        </div>
    </div>
</div>

<template id="tplBaris">
    <tr>
        <td>
            <input type="hidden" name="product_id[]" class="pid">
            <button type="button" class="btn btn-outline-secondary btn-sm w-100 text-start d-flex justify-content-between align-items-center gap-2 btn-pilih-menu">
                <span class="label-menu text-truncate text-muted">Pilih menu</span>
                <i class="bi bi-search flex-shrink-0"></i>
            </button>
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
    const PRODUK = <?= json_encode($petaProduk, $jsonFlag) ?>;
    const oldItems = <?= json_encode($oldItems, $jsonFlag) ?>;
    const oldMeja = <?= json_encode($oldMeja, $jsonFlag) ?>;

    const $ = id => document.getElementById(id);
    const tbody = $('barisItem'),
        tpl = $('tplBaris'),
        form = $('formTransaksi');
    const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const modalMeja = new bootstrap.Modal($('modalMeja'));
    const modalMenu = new bootstrap.Modal($('modalMenu'));
    let grand = 0;
    let barisAktif = null;
    let kategoriAktif = '';

    function setMeja(no, kursi) {
        $('nomorMeja').value = no;
        const label = $('labelMeja');
        label.textContent = no ? `Meja ${no} · ${kursi} kursi` : 'Pilih meja';
        label.classList.toggle('text-muted', !no);
        document.querySelectorAll('.meja').forEach(b => b.classList.toggle('dipilih', b.dataset.no === String(no)));
        if (no) $('pesanMeja').classList.add('d-none');
    }

    document.querySelectorAll('.meja').forEach(b => {
        b.addEventListener('click', () => {
            setMeja(b.dataset.no, b.dataset.kursi);
            modalMeja.hide();
        });
    });

    function saringMenu() {
        const q = $('cariMenu').value.trim().toLowerCase();
        let ada = false;
        document.querySelectorAll('.item-menu').forEach(el => {
            const cocok = (!kategoriAktif || el.dataset.kategori === kategoriAktif) && el.dataset.cari.includes(q);
            el.classList.toggle('d-none', !cocok);
            if (cocok) ada = true;
        });
        $('menuKosong').classList.toggle('d-none', ada);
    }

    function bukaMenu(tr) {
        barisAktif = tr;
        const pidSekarang = tr.querySelector('.pid').value;
        document.querySelectorAll('.kartu-menu').forEach(k => k.classList.toggle('dipilih', k.dataset.id === pidSekarang));
        $('cariMenu').value = '';
        kategoriAktif = '';
        document.querySelectorAll('.pil-kategori').forEach(p => p.classList.toggle('active', p.dataset.kategori === ''));
        saringMenu();
        modalMenu.show();
    }

    function setProduk(tr, id) {
        const p = PRODUK[id];
        if (!p) return;
        tr.querySelector('.pid').value = id;
        const label = tr.querySelector('.label-menu');
        label.textContent = `${p.nama} · ${fmt(p.harga)}`;
        label.classList.remove('text-muted');
        const qty = tr.querySelector('.qty');
        qty.max = p.stok;
        if (Number(qty.value) > p.stok) qty.value = p.stok;
        hitung();
    }

    $('cariMenu').addEventListener('input', saringMenu);
    $('pilKategori').addEventListener('click', e => {
        const tombol = e.target.closest('.pil-kategori');
        if (!tombol) return;
        kategoriAktif = tombol.dataset.kategori;
        document.querySelectorAll('.pil-kategori').forEach(p => p.classList.toggle('active', p === tombol));
        saringMenu();
    });
    $('daftarMenu').addEventListener('click', e => {
        const kartu = e.target.closest('.kartu-menu');
        if (!kartu || !barisAktif) return;
        setProduk(barisAktif, kartu.dataset.id);
        modalMenu.hide();
    });
    $('modalMenu').addEventListener('shown.bs.modal', () => $('cariMenu').focus());

    function tambahBaris(pid = '', qty = 1, catatan = '') {
        const tr = tpl.content.firstElementChild.cloneNode(true);
        tr.querySelector('.qty').value = qty;
        tr.querySelector('[name="catatan_khusus[]"]').value = catatan;
        tbody.appendChild(tr);
        if (pid && PRODUK[pid]) setProduk(tr, pid);
        hitung();
    }

    function hitung() {
        let subtotal = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const p = PRODUK[tr.querySelector('.pid').value];
            const harga = p ? p.harga : 0;
            const baris = harga * (Number(tr.querySelector('.qty').value) || 0);
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
        $('nomorMeja').disabled = !isDineIn();
        $('wrapTunai').classList.toggle('d-none', !isTunai());
        $('bayar').disabled = !isTunai();
        hitung();
    }

    tbody.addEventListener('input', hitung);
    tbody.addEventListener('change', hitung);
    tbody.addEventListener('click', e => {
        const hapus = e.target.closest('.hapus-baris');
        if (hapus) {
            if (tbody.rows.length > 1) {
                hapus.closest('tr').remove();
                hitung();
            }
            return;
        }
        const pilih = e.target.closest('.btn-pilih-menu');
        if (pilih) bukaMenu(pilih.closest('tr'));
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
        if (isDineIn() && !$('nomorMeja').value) {
            e.preventDefault();
            $('pesanMeja').classList.remove('d-none');
            modalMeja.show();
            return;
        }
        const adaBarisKosong = [...tbody.querySelectorAll('.pid')].some(i => !i.value);
        if (grand <= 0 || adaBarisKosong) {
            e.preventDefault();
            alert(grand <= 0 ? 'Pilih minimal satu menu.' : 'Pilih menu pada semua baris, atau hapus baris yang kosong.');
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

    const mejaLama = oldMeja ? document.querySelector(`.meja[data-no="${CSS.escape(oldMeja)}"]:not(:disabled)`) : null;
    if (mejaLama) setMeja(mejaLama.dataset.no, mejaLama.dataset.kursi);
    aturTampilan();
</script>
<?= $this->endSection() ?>