<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Database\Seeders\ProdutosSeeder;
use Database\Seeders\ClientesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

       $this->call([
            ClientesSeeder::class,
            ProdutosSeeder::class,
       ]);

    }
}
