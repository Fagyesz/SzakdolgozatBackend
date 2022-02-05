<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;
use JetBrains\PhpStorm\ArrayShape;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    #[ArrayShape(['id' => 'string', 'name' => 'string', 'description' => 'string'])]
    public function definition() : array
    {
        return [
            'id' => $this->faker->userName(),
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
