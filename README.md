# Sistem Kasir & Pemesanan Kafe Modern — Titik Temu Kopi

Aplikasi web Point of Sales (POS) Dine-In & Takeaway untuk Kafe "Titik Temu Kopi".
Dibuat untuk Tema 01 (kode MED-WEB-01): pencatatan pesanan, perhitungan tagihan otomatis, pemantauan omset harian, dan laporan penjualan.

## Tech Stack

| Bagian     | Teknologi                                            |
| ---------- | ---------------------------------------------------- |
| Bahasa     | PHP 8.1+                                             |
| Framework  | CodeIgniter 4 (arsitektur MVC)                       |
| Database   | MySQL / MariaDB                                      |
| Frontend   | Bootstrap 5.3, Bootstrap Icons, Chart.js 4 (via CDN) |
| Ekspor PDF | Dompdf (dompdf/dompdf)                               |
| Password   | Bcrypt (password_hash)                               |

## Fitur

### 1. Autentikasi & Hak Akses

- Form login dengan validasi input dan pesan error (username/password salah)
- Password disimpan sebagai hash Bcrypt, tidak ada plaintext
- Proteksi route lewat Filter (auth dan role:admin): dashboard, transaksi, dan laporan tidak bisa dibuka tanpa login
- Logout menghancurkan session dan kembali ke halaman login
- Proteksi CSRF pada semua form POST
- Dua role:

| Fitur                                        | Admin / Manager | Kasir / Waiter |
| -------------------------------------------- | :-------------: | :------------: |
| Dashboard                                    |        ✔        |       –        |
| Kelola kategori & produk (menu, harga, stok) |        ✔        |       –        |
| Kelola akun pengguna                         |        ✔        |       –        |
| Laporan & ekspor PDF/Excel                   |        ✔        |       –        |
| Buat pesanan, terima pembayaran, cetak struk |        ✔        |       ✔        |
| Riwayat transaksi                            |      Semua      | Milik sendiri  |

### 2. Dashboard

- 4 KPI: Total Omset Hari Ini, Jumlah Pesanan (status selesai hari ini), Menu Terlaris (bulan berjalan), Peringatan Stok Rendah
- Grafik batang omset 7 hari terakhir (Chart.js)
- Tabel 10 transaksi terbaru beserta status
- Tombol aksi cepat "Buat Transaksi Baru"

### 3. Transaksi Inti (Master–Detail)

- Nomor invoice otomatis INV-POS-YYYYMMDD-XXXX (urutan reset per hari, unik)
- Tipe pesanan Dine-In (wajib pilih meja) atau Takeaway
- Nama Pemesan / Catatan Meja
- Item dinamis: pilih menu, qty, catatan khusus (mis. Less Sugar)
- Kalkulasi otomatis: subtotal per baris, pajak PB1 10%, diskon/voucher, grand total
- Pembayaran: Tunai (kembalian otomatis), QRIS, Transfer Bank, Kartu Debit
- Status pesanan: Diproses, Selesai, Dibatalkan
- Struk siap cetak: thermal 80mm, 58mm, atau A4

Validasi & integritas data

- Stok dicek sebelum transaksi disimpan (baris produk dikunci FOR UPDATE agar tidak terjadi stok ganda)
- Pembayaran tunai yang kurang dari total ditolak
- Header, detail, dan pengurangan stok disimpan dalam satu database transaction (commit/rollback)
- Semua nilai uang dihitung ulang di server dari harga produk, bukan dari input browser
- Pesanan yang dibatalkan otomatis mengembalikan stok

### 4. Laporan & Ekspor

- Filter: rentang tanggal, kategori menu, metode pembayaran
- Summary bar: Omset Bruto, Total Diskon, Pajak Terkumpul, Omset Bersih, Rata-rata Nilai Transaksi
- Kolom tabel: No. Faktur, Tanggal & Jam, Kasir, Tipe Pesanan, Item Terjual, Subtotal, Pajak, Grand Total, Status
- Ekspor PDF dan Excel/CSV

## Struktur Folder

app/
├── Config/
│ └── Routes.php
├── Controllers/
│ ├── Auth.php Dashboard.php Kategori.php Produk.php
│ └── User.php Transaksi.php Laporan.php
├── Filters/
│ └── RoleFilter.php
├── Helpers/
│ └── format_helper.php
├── Models/
│ ├── UserModel.php CategoryModel.php ProductModel.php
│ └── OrderModel.php OrderDetailModel.php
└── Views/
├── layouts/main.php
├── auth/login.php
├── dashboard/index.php
├── kategori/ index.php form.php
├── produk/ index.php form.php
├── user/ index.php form.php
├── transaksi/ index.php baru.php detail.php struk.php
└── laporan/ index.php pdf.php
db_pos_kafe.sql

## Skema Database

Database db_pos_kafe terdiri dari 5 tabel:

| Tabel         | Keterangan                                        |
| ------------- | ------------------------------------------------- |
| users         | Akun pengguna (role admin / kasir, password hash) |
| categories    | Kategori menu (Coffee, Non-Coffee, Food, Pastry)  |
| products      | Master menu: harga jual, harga pokok, stok, unit  |
| orders        | Header transaksi                                  |
| order_details | Rincian item pesanan                              |

Relasi: categories 1─N products, users 1─N orders, orders 1─N order_details, products 1─N order_details.

## Instalasi

### Prasyarat

- PHP 8.1 atau lebih baru dengan ekstensi intl, mbstring, mysqlnd, dom
- Composer
- MySQL
- Koneksi internet saat dijalankan (Bootstrap, ikon, dan Chart.js dimuat dari CDN)

### Langkah

1. Clone Repository
   bash
   git clone https://github.com/adittyadev/sistem-kasir.git

2. Masuk ke Folder Proyek
   bash
   cd sistem-kasir

3. Pindahkan ke Folder Web Server
   bash
   C:\laragon2\www\sistem-kasir

4. Import database

- Buka MySQL.
- Buat database dengan nama sesuai konfigurasi aplikasi.
- Import file db_pos_kafe.sql.

5. Atur environment
   bash
   cp env .env

Edit .env:
ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_pos_kafe
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

6. Daftarkan Filter dan aktifkan CSRF — app/Config/Filters.php
   php
   use App\Filters\RoleFilter;

public array $aliases = [
// ...alias bawaan...
'auth' => RoleFilter::class,
'role' => RoleFilter::class,
];

7. Daftarkan helper — app/Controllers/BaseController.php
   php
   protected $helpers = ['url', 'form', 'format'];

8. Atur zona waktu dan URL — app/Config/App.php
   php
   public string $appTimezone = 'Asia/Jakarta';
   public string $indexPage = '';

9. Jalankan aplikasi
   bash
   php spark serve

Buka http://localhost:8080 lalu login dengan akun uji coba di bawah.

## Akun Uji Coba

| Role                    | Username | Password |
| ----------------------- | -------- | -------- |
| Administrator / Manager | admin    | password |
| Kasir / Waiter          | kasir    | password |

## Alur Pengujian yang Disarankan

1. Login sebagai kasir, lalu coba buka /dashboard — harus dialihkan kembali ke form transaksi.
2. Buat transaksi Dine-In dengan beberapa item, isi catatan, dan bayar tunai. Pastikan total, pajak, dan kembalian benar.
3. Coba bayar tunai kurang dari total — harus ditolak.
4. Coba pesan melebihi stok — harus ditolak dan tidak ada data yang tersimpan.
5. Buka detail transaksi, tandai Selesai, lalu cetak struk (80mm, 58mm, A4).
6. Batalkan sebuah transaksi — stok produk harus kembali.
7. Login sebagai admin: cek dashboard, tambah/ubah menu, atur stok, lalu buat laporan dengan filter dan ekspor PDF/CSV.
8. Logout, lalu buka /dashboard langsung — harus dialihkan ke halaman login.

## Aturan Bisnis

- Pajak PB1 10% dihitung dari subtotal sebelum diskon.
- Grand Total = Subtotal + Pajak − Diskon.
- Omset Bersih (laporan) = Omset Bruto − Total Diskon.
- Dashboard dan summary laporan hanya menghitung pesanan berstatus Selesai.
- Pesanan baru berstatus Diproses dan diubah ke Selesai oleh kasir/admin dari halaman detail.
- Stok rendah: stok ≤ 10 (konstanta ProductModel::BATAS_STOK_RENDAH).
- Kategori yang masih dipakai produk dan produk yang sudah pernah terjual tidak dapat dihapus.
- Akun pengguna tidak dihapus (riwayat transaksi mengacu ke akun); cukup dinonaktifkan.

## Catatan

- Data contoh pada database.sql memuat 5 transaksi bertanggal 15–17 November 2026. Angka "hari ini" di dashboard hanya terisi jika tanggal server sama dengan tanggal data contoh.
- Skema database mengikuti rekomendasi soal, dengan satu tambahan kolom orders.nama_pemesan untuk input "Nama Pemesan / Catatan Meja" yang diminta pada alur form transaksi.
