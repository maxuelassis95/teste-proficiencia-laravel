<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminsSeeder extends Seeder
{

    public function run(): void
    {
        Admin::create([
            'nome' => 'Admin',
            'email' => 'admin@teste.com',
            'password' => bcrypt('senha123'),
        ]);
    }
}
