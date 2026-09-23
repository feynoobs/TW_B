<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,
            BoardSeeder::class,
            ThreadSeeder::class,
            ResponseSeeder::class,
            // 今後 ThreadSeeder や PostSeeder を作ったらここに追記していく
        ]);
    }
}
