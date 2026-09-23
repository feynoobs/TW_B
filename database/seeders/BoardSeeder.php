<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;
use Faker\Factory;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('ja_JP');
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                Board::create([
                    'slug' => "board_{$i}_{$j}",
                    'name' => $faker->realText(10),
                    'name_nns' => "ななし{$i}_{$j}",
                    'sort' => $j,
                    'status' => 0,
                    'group_id' => $i,
                ]);
            }
        }
    }
}
