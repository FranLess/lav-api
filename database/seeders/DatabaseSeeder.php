<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->disableForeignKeys();
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
            CommentSeeder::class
        ]);
        $this->enableForeignKeys();
    }
}
