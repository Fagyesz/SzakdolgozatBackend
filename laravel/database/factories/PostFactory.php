<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Silber\Bouncer\Database\Role;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition() : array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(random_int(1, 4), true),
            'user_id' => Role::where('name', 'assistant')->orWhere('name', 'superuser')->orWhere('name', 'server')->inRandomOrder()->first()->users()->inRandomOrder()->first()->id,
            'published_at' => $this->faker->dateTimeBetween('-1 month', '+1 week')
        ];
    }
}
