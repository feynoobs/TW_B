<?php

namespace Database\Seeders;

use App\Models\Response;
use Illuminate\Database\Seeder;
use Faker\Factory;

class ResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('ja_JP');

        for ($j = 1; $j <= 75; $j++) {
            for ($k = 1; $k <= 100; $k++) {
                Response::create([
                    'name' => $faker->realText(10),
                    'mail' => $faker->realText(10),
                    'hash' => $faker->realText(10),
                    'content' => $faker->realText(1000),
                    'user_agent' => $faker->word(),
                    'user_domain' => $faker->word(),
                    'ip_address' => $faker->ipv4(),
                    'status' => 0,
                    'thread_id' => $j,
                ]);
            }
        }
    }
}
