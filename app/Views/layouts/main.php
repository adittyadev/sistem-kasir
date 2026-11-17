<?php
$menu = [
    ['/dashboard',      'Dashboard',      'bi-speedometer2', 'dashboard*',     ['admin']],
    ['/transaksi/baru', 'Transaksi Baru', 'bi-cart-plus',    'transaksi/baru', ['admin', 'kasir']],
    ['/transaksi',      'Riwayat',        'bi-receipt',      'transaksi',      ['admin', 'kasir']],
    ['/produk',         'Produk',         'bi-cup-hot',      'produk*',        ['admin']],
    ['/kategori',       'Kategori',       'bi-tags',         'kategori*',      ['admin']],
    ['/laporan',        'Laporan',        'bi-bar-chart',    'laporan*',       ['admin']],
    ['/user',           'Pengguna',       'bi-people',       'user*',          ['admin']],
];
$errors  = session()->getFlashdata('errors') ?? [];
$nama    = (string) session('nama_lengkap');
$inisial = mb_strtoupper(mb_substr($nama, 0, 1)) ?: '?';
$beranda = site_url(session('role') === 'admin' ? 'dashboard' : 'transaksi/baru');
$jsonFlag = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> · Titik Temu Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --espresso: #2a1a12;
            --sidebar-bg: #35221a;
            --kopi: #8b5a3c;
            --kopi-gelap: #734a31;
            --krema: #e0b184;
            --lebar-sidebar: 250px;
            --tinggi-navbar: 60px;
        }

        body {
            background: #f6f2ee;
        }

        .navbar-kopi {
            background: var(--espresso);
            height: var(--tinggi-navbar);
            padding: 0 1rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .merek {
            font-family: "Bricolage Grotesque", system-ui, sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .6rem;
            white-space: nowrap;
        }

        .merek i {
            color: var(--krema);
        }

        .merek:hover {
            color: #fff;
        }

        .avatar {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--kopi);
            color: #fff;
            font-weight: 600;
            font-size: .95rem;
        }

        .btn-keluar {
            color: #e8d9cc;
            border: 1px solid rgba(255, 255, 255, .3);
            background: transparent;
        }

        .btn-keluar:hover {
            color: #fff;
            background: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .45);
        }

        .btn-keluar:focus-visible,
        .sidebar-nav .nav-link:focus-visible,
        .merek:focus-visible {
            outline: 2px solid var(--krema);
            outline-offset: 2px;
        }

        .sidebar {
            --bs-offcanvas-bg: var(--sidebar-bg);
            --bs-offcanvas-color: #e8d9cc;
            --bs-offcanvas-width: var(--lebar-sidebar);
            background: var(--sidebar-bg);
            color: #e8d9cc;
        }

        .sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: .2rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem .9rem;
            border-radius: .6rem;
            color: #d9c7b8;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, .07);
        }

        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(224, 177, 132, .16);
            border-left-color: var(--krema);
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                top: var(--tinggi-navbar);
                left: 0;
                bottom: 0;
                z-index: 1010;
                width: var(--lebar-sidebar) !important;
                height: auto !important;
                background: var(--sidebar-bg) !important;
                flex-grow: 0;
                padding: 0;
            }

            .konten {
                margin-left: var(--lebar-sidebar);
            }
        }

        .card {
            border: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
            border-radius: .75rem;
        }

        .kpi-ikon {
            width: 46px;
            height: 46px;
            border-radius: .75rem;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
        }

        .btn-kopi {
            background: var(--kopi);
            color: #fff;
        }

        .btn-kopi:hover {
            background: var(--kopi-gelap);
            color: #fff;
        }
    </style>
</head>

<body>
    <header class="navbar-kopi sticky-top">
        <button class="btn btn-outline-light btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Buka menu">
            <i class="bi bi-list fs-5"></i>
        </button>

        <a class="merek" href="<?= $beranda ?>"><i class="bi bi-cup-hot-fill"></i>Titik Temu Kopi</a>

        <form action="<?= site_url('logout') ?>" method="post" class="ms-auto d-flex align-items-center gap-2 gap-sm-3">
            <?= csrf_field() ?>
            <div class="d-flex align-items-center gap-2 min-w-0">
                <div class="avatar" aria-hidden="true"><?= esc($inisial) ?></div>
                <div class="d-none d-sm-block lh-sm text-truncate" style="max-width:180px">
                    <div class="text-white small fw-medium text-truncate"><?= esc($nama) ?></div>
                    <div class="small" style="color:#cdb9a8"><?= esc(ucfirst((string) session('role'))) ?></div>
                </div>
            </div>
            <button class="btn btn-keluar btn-sm" type="submit" aria-label="Keluar">
                <i class="d-none d-sm-inline ms-1"></i>Keluar
            </button>
        </form>
    </header>

    <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Menu utama">
        <div class="offcanvas-header d-lg-none px-3 py-3">
            <span class="fw-semibold text-white">Menu</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Tutup menu"></button>
        </div>

        <div class="offcanvas-body px-3 pb-3 pt-0 pt-lg-3">
            <nav aria-label="Navigasi">
                <ul class="sidebar-nav">
                    <?php foreach ($menu as [$url, $label, $ikon, $pola, $peran]): ?>
                        <?php if (in_array(session('role'), $peran, true)): ?>
                            <li>
                                <a class="nav-link <?= url_is($pola) ? 'active' : '' ?>" href="<?= site_url($url) ?>"
                                    <?= url_is($pola) ? 'aria-current="page"' : '' ?>>
                                    <i class="bi <?= $ikon ?>"></i><?= $label ?>
                                </a>
                            </li>
                        <?php endif ?>
                    <?php endforeach ?>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="konten">
        <main class="container-xl py-4">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script>
        (function() {
            const Kopi = Swal.mixin({
                confirmButtonColor: '#8b5a3c',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'rounded-4'
                }
            });
            window.Kopi = Kopi;

            <?php if ($pesan = session()->getFlashdata('success')): ?>
                Kopi.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: <?= json_encode($pesan, $jsonFlag) ?>,
                    timer: 2200,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            <?php endif ?>

            <?php if ($pesan = session()->getFlashdata('error')): ?>
                Kopi.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: <?= json_encode($pesan, $jsonFlag) ?>,
                    confirmButtonText: 'Mengerti'
                });
            <?php endif ?>

            <?php if ($errors): ?>
                    (function() {
                        const daftar = <?= json_encode(array_values((array) $errors), $jsonFlag) ?>;
                        const ul = document.createElement('ul');
                        ul.className = 'text-start mb-0';
                        daftar.forEach(function(teks) {
                            const li = document.createElement('li');
                            li.textContent = teks;
                            ul.appendChild(li);
                        });
                        Kopi.fire({
                            icon: 'error',
                            title: 'Periksa kembali isian Anda',
                            html: ul,
                            confirmButtonText: 'Mengerti'
                        });
                    })();
            <?php endif ?>

            document.addEventListener('submit', function(e) {
                const f = e.target;
                if (!f.dataset || !f.dataset.konfirmasi) return;
                e.preventDefault();
                Kopi.fire({
                    icon: f.dataset.ikon || 'warning',
                    title: f.dataset.judul || 'Yakin?',
                    text: f.dataset.konfirmasi,
                    showCancelButton: true,
                    confirmButtonText: f.dataset.tombol || 'Ya, lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#b3392b',
                    focusCancel: true
                }).then(function(hasil) {
                    if (hasil.isConfirmed) HTMLFormElement.prototype.submit.call(f);
                });
            });
        })();
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>