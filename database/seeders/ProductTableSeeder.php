<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $categoryIds = DB::table('category')->pluck('id')->toArray();

        if (empty($categoryIds)) {
            $this->command->warn('No hay categorías en la tabla category');

            return;
        }

        $products = [];

        for ($i = 1; $i <= 50; $i++) {
            $products[] = [
                'name' => $faker->word,
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 10, 500),
                'stock' => $faker->numberBetween(5, 100),
                'category_id' => $faker->randomElement($categoryIds),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('product')->insert($products);
    }
}
