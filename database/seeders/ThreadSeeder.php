<?php

namespace Database\Seeders;

use App\Models\Thread;
use Illuminate\Database\Seeder;
use Faker\Factory;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('ja_JP');

        for ($i = 1; $i <= 15; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                Thread::create([
                    'title' => $faker->realText(10),
                    'sort' => $j,
                    'status' => 0,
                    'board_id' => $i,
                ]);
            }
        }
    }
}
