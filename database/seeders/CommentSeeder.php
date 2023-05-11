<?php

namespace Database\Seeders;

use App\Models\Comment;
use Database\Factories\Helpers\FactoryHelper;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    use TruncateTable;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncate('comments');
        Comment::factory(10)->create();
    }
}
