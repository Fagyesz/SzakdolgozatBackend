<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TenantSeeder::class);
        Tenant::all()->runForEach(function (Tenant $tenant){
            $this->call(UserSeeder::class);
            $this->call(BouncerSeeder::class);
            $this->call(PostSeeder::class);
            $this->call(ServiceSeeder::class);
            $this->call(AppointmentSeeder::class);
        });
    }
}
