<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;
use Faker\Factory;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('ja_JP');
        for ($i = 1; $i <= 5; $i++) {
            Group::create([
                'slug' => "group_{$i}",
                'name' => $faker->realText(10),
                'sort' => $i,
                'status' => 0,
            ]);
        }
    }
}
