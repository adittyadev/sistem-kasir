<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Titik Temu Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --espresso: #2a1a12;
            --kopi: #8b5a3c;
            --kopi-gelap: #6f452c;
            --krema: #e0b184;
            --garis: #e4dcd4;
            --teks: #2a1a12;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background: var(--espresso);
            color: var(--teks);
            position: relative;
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            width: min(70vw, 520px);
            aspect-ratio: 1;
            border-radius: 50%;
            pointer-events: none;
        }

        body::before {
            left: -12%;
            top: -18%;
            border: 1.5px solid rgba(224, 177, 132, .35);
        }

        body::after {
            right: -12%;
            bottom: -18%;
            border: 1.5px solid rgba(224, 177, 132, .35);
            background: rgba(139, 90, 60, .22);
        }

        .kartu-login {
            position: relative;
            z-index: 1;
            width: min(400px, 100%);
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .35);
        }

        .logo-bulat {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: 0 auto .85rem;
            background: var(--espresso);
            color: var(--krema);
            font-size: 1.5rem;
        }

        .judul {
            font-family: "Bricolage Grotesque", system-ui, sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: -.01em;
        }

        .form-control {
            border-color: var(--garis);
            padding: .7rem .9rem;
            border-radius: .6rem;
        }

        .input-group .form-control {
            border-radius: .6rem 0 0 .6rem;
        }

        .btn-mata {
            border: 1px solid var(--garis);
            border-left: 0;
            background: #fff;
            color: #6d5f57;
            border-radius: 0 .6rem .6rem 0;
        }

        .form-control:focus {
            border-color: var(--kopi);
            box-shadow: 0 0 0 .2rem rgba(139, 90, 60, .18);
        }

        .btn-mata:focus-visible,
        .btn-kopi:focus-visible {
            outline: 3px solid rgba(139, 90, 60, .4);
            outline-offset: 2px;
        }

        .btn-kopi {
            background: var(--kopi);
            color: #fff;
            padding: .75rem 1rem;
            border: 0;
            border-radius: .6rem;
            font-weight: 600;
        }

        .btn-kopi:hover,
        .btn-kopi:active {
            background: var(--kopi-gelap);
            color: #fff;
        }

        .btn-kopi:disabled {
            background: var(--kopi);
            opacity: .75;
        }
    </style>
</head>

<body>
    <div class="card kartu-login">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <div class="logo-bulat"><i class="bi bi-cup-hot-fill"></i></div>
                <h1 class="judul mb-1">Titik Temu Kopi</h1>
                <div class="text-secondary small">Sistem Kasir &amp; Pemesanan</div>
            </div>

            <form action="<?= site_url('login') ?>" method="post" id="form-login">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-medium" for="username">Username</label>
                    <input class="form-control" id="username" name="username"
                        value="<?= esc(old('username')) ?>"
                        autocomplete="username" autocapitalize="none" spellcheck="false"
                        autofocus required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium" for="password">Password</label>
                    <div class="input-group">
                        <input class="form-control" id="password" name="password" type="password"
                            autocomplete="current-password" required>
                        <button class="btn btn-mata" type="button" id="toggle-password"
                            aria-label="Tampilkan password" aria-pressed="false">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <button class="btn btn-kopi w-100" type="submit" id="tombol-masuk">
                    <span class="teks-normal">Masuk</span>
                    <span class="teks-proses d-none">
                        <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses…
                    </span>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <?php if ($pesan = session()->getFlashdata('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal masuk',
                text: <?= json_encode($pesan, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                confirmButtonText: 'Coba lagi',
                confirmButtonColor: '#8b5a3c',
                customClass: {
                    popup: 'rounded-4'
                },
                didClose: function() {
                    var u = document.getElementById('username');
                    if (u) u.focus();
                }
            });
        </script>
    <?php endif ?>

    <script>
        (function() {
            var pw = document.getElementById('password');
            var toggle = document.getElementById('toggle-password');
            var ikon = toggle.querySelector('i');

            toggle.addEventListener('click', function() {
                var tampil = pw.type === 'password';
                pw.type = tampil ? 'text' : 'password';
                ikon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
                toggle.setAttribute('aria-pressed', tampil);
                toggle.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
            });

            var form = document.getElementById('form-login');
            var tombol = document.getElementById('tombol-masuk');
            form.addEventListener('submit', function() {
                if (!form.checkValidity()) return;
                tombol.disabled = true;
                tombol.querySelector('.teks-normal').classList.add('d-none');
                tombol.querySelector('.teks-proses').classList.remove('d-none');
            });
        })();
    </script>
</body>

</html>