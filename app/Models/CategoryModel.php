<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_kategori', 'slug', 'status'];

    protected $validationRules = [
        'nama_kategori' => 'required|max_length[50]',
        'slug'          => 'required|max_length[60]|is_unique[categories.slug,id,{id}]',
        'status'        => 'required|in_list[aktif,nonaktif]',
    ];

    public function getAktif(): array
    {
        return $this->where('status', 'aktif')->orderBy('nama_kategori')->findAll();
    }
}
