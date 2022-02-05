<?php

namespace Database\Seeders;

use App\Models\Post;
use Exception;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(): void
    {
        Post::factory(50)->create();
        Post::sample(random_int(Post::count() * 0.25, Post::count() * 0.5));
    }
}
