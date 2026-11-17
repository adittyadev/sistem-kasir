<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderDetailModel extends Model
{
    protected $table         = 'order_details';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'product_id', 'harga_satuan', 'kuantiti', 'subtotal_baris', 'catatan_khusus'];

    public function getByOrder(int $orderId): array
    {
        return $this->select('order_details.*, products.nama_produk, products.unit')
            ->join('products', 'products.id = order_details.product_id')
            ->where('order_details.order_id', $orderId)
            ->findAll();
    }

    public function menuTerlaris(?string $dari = null, ?string $sampai = null, int $limit = 1): array
    {
        $this->select('products.nama_produk, SUM(order_details.kuantiti) AS total_terjual')
            ->join('orders', 'orders.id = order_details.order_id')
            ->join('products', 'products.id = order_details.product_id')
            ->where('orders.status', 'selesai');

        if ($dari) {
            $this->where('orders.created_at >=', $dari . ' 00:00:00');
        }
        if ($sampai) {
            $this->where('orders.created_at <=', $sampai . ' 23:59:59');
        }

        return $this->groupBy('order_details.product_id, products.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
