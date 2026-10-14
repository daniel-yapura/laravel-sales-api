<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category')->insert([
            [
                'name' => 'Comida',
                'slug' => 'comida',
            ],
            [
                'name' => 'Bebida',
                'slug' => 'bebida',
            ],
            [
                'name' => 'Alcohol',
                'slug' => 'alcohol',
            ],
        ]);
    }
}
