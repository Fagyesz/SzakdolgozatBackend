<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;
use Silber\Bouncer\Database\Role;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    #[ArrayShape(['name' => "string", 'description' => "string", 'duration' => "int", 'user_id' => "mixed"])]
    public function definition() : array
    {
        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'duration' => $this->faker->numberBetween(10, 120),
            'user_id' => Role::where('name', 'server')->first()->users()->inRandomOrder()->first()->id
        ];
    }
}
