<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['username', 'password', 'password_hash', 'nama_lengkap', 'role', 'status'];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $validationRules = [
        'username'     => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'nama_lengkap' => 'required|max_length[100]',
        'role'         => 'required|in_list[admin,kasir]',
        'status'       => 'required|in_list[aktif,nonaktif]',
    ];

    protected function hashPassword(array $event): array
    {
        $data = $event['data'];

        if (! empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        unset($data['password']);

        $event['data'] = $data;

        return $event;
    }

    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->where('username', $username)->where('status', 'aktif')->first();

        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            return null;
        }

        return $user;
    }
}
