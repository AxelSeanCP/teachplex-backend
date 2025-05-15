<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            "id" => "admin-1",
            "name" => "admin",
            "password" => password_hash("admin", PASSWORD_BCRYPT),
            "email" => "admin@gmail.com",
            "role" => "admin"
        ];

        $user = new User();
        $user->insert($data);
    }
}
