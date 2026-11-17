<?php
$v           = static fn(string $f, $default = '') => esc(old($f, $produk[$f] ?? $default));
$editMode    = (bool) $produk;
$kodeBerikut = $kodeBerikut ?? [];
$catTerpilih = (int) old('category_id', $produk['category_id'] ?? 0);
$jsonFlag    = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= $editMode ? 'Edit' : 'Tambah' ?> Produk<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .kartu-kategori {
        display: flex;
        align-items: center;
        gap: .85rem;
        width: 100%;
        padding: .8rem .9rem;
        text-align: left;
        color: inherit;
        background: #fff;
        border: 1.5px solid #e4dcd4;
        border-radius: .75rem;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }

    .kartu-kategori:hover {
        border-color: var(--kopi);
        box-shadow: 0 3px 10px rgba(42, 26, 18, .1);
    }

    .kartu-kategori:focus-visible {
        outline: 3px solid rgba(139, 90, 60, .4);
        outline-offset: 2px;
    }

    .kartu-kategori.dipilih {
        border-color: var(--kopi);
        background: #fbf3ec;
        box-shadow: 0 0 0 3px rgba(139, 90, 60, .15);
    }

    .lencana-prefix {
        flex: 0 0 52px;
        height: 52px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: #2a1a12;
        color: #e0b184;
        font-weight: 700;
        font-size: .85rem;
        letter-spacing: .02em;
    }

    .kartu-kategori.dipilih .lencana-prefix {
        background: var(--kopi);
        color: #fff;
    }

    @media (prefers-reduced-motion: reduce) {
        .kartu-kategori {
            transition: none;
        }
    }
</style>

<h1 class="h4 mb-3"><?= $editMode ? 'Edit' : 'Tambah' ?> Produk</h1>
<div class="card">
    <div class="card-body">
        <form method="post" id="formProduk" action="<?= site_url($editMode ? "produk/{$produk['id']}/update" : 'produk/simpan') ?>" class="row g-3">
            <?= csrf_field() ?>

            <div class="col-md-6">
                <label class="form-label" for="btnPilihKategori">Kategori</label>
                <input type="hidden" name="category_id" id="categoryId" value="<?= $catTerpilih ?: '' ?>">
                <button type="button" class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center"
                    id="btnPilihKategori" data-bs-toggle="modal" data-bs-target="#modalKategori">
                    <span id="labelKategori" class="text-muted">Pilih kategori</span>
                    <i class="bi bi-tags"></i>
                </button>
                <div class="text-danger small mt-1 d-none" id="pesanKategori">Pilih kategori terlebih dahulu.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="kodeProduk">Kode Produk</label>
                <input class="form-control bg-light" id="kodeProduk" readonly
                    value="<?= $editMode ? esc($produk['kode_produk']) : '' ?>" placeholder="Dibuat otomatis">
                <div class="form-text" id="infoKode">
                    <?= $editMode ? 'Kode tidak dapat diubah.' : 'Dibuat otomatis dari kategori yang dipilih.' ?>
                </div>
            </div>

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

<div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="judulModalKategori" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="judulModalKategori">Pilih Kategori</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <?php foreach ($kategori as $k): ?>
                        <?php $prefix = \App\Models\ProductModel::prefixKategori($k); ?>
                        <button type="button" class="kartu-kategori" data-id="<?= $k['id'] ?>" data-nama="<?= esc($k['nama_kategori']) ?>">
                            <span class="lencana-prefix" aria-hidden="true"><?= esc($prefix) ?></span>
                            <span>
                                <span class="d-block fw-medium"><?= esc($k['nama_kategori']) ?></span>
                                <span class="d-block small text-muted">
                                    <?php if (! $editMode && isset($kodeBerikut[$k['id']])): ?>
                                        Kode berikutnya: <?= esc($kodeBerikut[$k['id']]) ?>
                                    <?php else: ?>
                                        Awalan kode: <?= esc($prefix) ?>
                                    <?php endif ?>
                                </span>
                            </span>
                        </button>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const KODE = <?= json_encode($kodeBerikut, $jsonFlag) ?>;
    const EDIT = <?= json_encode($editMode) ?>;
    const $ = id => document.getElementById(id);
    const modalKategori = new bootstrap.Modal($('modalKategori'));

    function setKategori(id, nama) {
        $('categoryId').value = id;
        const label = $('labelKategori');
        label.textContent = nama;
        label.classList.remove('text-muted');
        document.querySelectorAll('.kartu-kategori').forEach(k => k.classList.toggle('dipilih', k.dataset.id === String(id)));
        $('pesanKategori').classList.add('d-none');
        if (!EDIT && KODE[id]) $('kodeProduk').value = KODE[id];
    }

    document.querySelectorAll('.kartu-kategori').forEach(k => {
        k.addEventListener('click', () => {
            setKategori(k.dataset.id, k.dataset.nama);
            modalKategori.hide();
        });
    });

    $('formProduk').addEventListener('submit', e => {
        if (!$('categoryId').value) {
            e.preventDefault();
            $('pesanKategori').classList.remove('d-none');
            modalKategori.show();
        }
    });

    const awal = $('categoryId').value ? document.querySelector(`.kartu-kategori[data-id="${CSS.escape($('categoryId').value)}"]`) : null;
    if (awal) setKategori(awal.dataset.id, awal.dataset.nama);
</script>
<?= $this->endSection() ?>