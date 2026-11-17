<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    public const BATAS_STOK_RENDAH = 10;

    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'category_id',
        'kode_produk',
        'nama_produk',
        'harga_jual',
        'harga_pokok',
        'stok',
        'unit',
    ];

    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'kode_produk' => 'required|max_length[20]|is_unique[products.kode_produk,id,{id}]',
        'nama_produk' => 'required|max_length[100]',
        'harga_jual'  => 'required|numeric|greater_than_equal_to[0]',
        'harga_pokok' => 'required|numeric|greater_than_equal_to[0]',
        'stok'        => 'required|integer|greater_than_equal_to[0]',
        'unit'        => 'required|max_length[20]',
    ];

    public static function prefixKategori(array $kategori): string
    {
        $bersih = static fn($teks): string => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $teks));

        $prefix = $bersih($kategori['kode_prefix'] ?? '');
        if ($prefix === '') {
            $prefix = substr($bersih($kategori['nama_kategori'] ?? ''), 0, 3);
        }

        return str_pad($prefix, 3, 'X');
    }

    public function buatKode(array $kategori): string
    {
        $prefix = self::prefixKategori($kategori);

        $baris = $this->db->query(
            'SELECT MAX(CAST(SUBSTRING(kode_produk, ?) AS UNSIGNED)) AS maks FROM products WHERE kode_produk LIKE ?',
            [strlen($prefix) + 2, $prefix . '-%']
        )->getRowArray();

        return sprintf('%s-%03d', $prefix, ((int) ($baris['maks'] ?? 0)) + 1);
    }

    public function kodeBerikutPerKategori(array $daftarKategori): array
    {
        $hasil = [];
        foreach ($daftarKategori as $k) {
            $hasil[$k['id']] = $this->buatKode($k);
        }

        return $hasil;
    }

    public function getWithCategory(?int $categoryId = null, ?string $keyword = null): array
    {
        $this->select('products.*, categories.nama_kategori')
            ->join('categories', 'categories.id = products.category_id');

        if ($categoryId) {
            $this->where('products.category_id', $categoryId);
        }
        if ($keyword) {
            $this->groupStart()
                ->like('products.nama_produk', $keyword)
                ->orLike('products.kode_produk', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('products.nama_produk')->findAll();
    }

    public function getTersedia(): array
    {
        return $this->select('products.*, categories.nama_kategori')
            ->join('categories', 'categories.id = products.category_id')
            ->where('categories.status', 'aktif')
            ->where('products.stok >', 0)
            ->orderBy('categories.nama_kategori')
            ->orderBy('products.nama_produk')
            ->findAll();
    }

    public function getStokRendah(int $batas = self::BATAS_STOK_RENDAH): array
    {
        return $this->where('stok <=', $batas)->orderBy('stok')->findAll();
    }

    public function countStokRendah(int $batas = self::BATAS_STOK_RENDAH): int
    {
        return $this->where('stok <=', $batas)->countAllResults();
    }

    public function lockById(int $id): ?array
    {
        return $this->db->query('SELECT * FROM products WHERE id = ? FOR UPDATE', [$id])->getRowArray();
    }

    public function kurangiStok(int $id, int $qty): bool
    {
        $this->db->query('UPDATE products SET stok = stok - ? WHERE id = ? AND stok >= ?', [$qty, $id, $qty]);

        return $this->db->affectedRows() === 1;
    }

    public function tambahStok(int $id, int $qty): void
    {
        $this->db->query('UPDATE products SET stok = stok + ? WHERE id = ?', [$qty, $id]);
    }
}
