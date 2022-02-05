<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Artisan;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('tenants:clear');
        Tenant::factory(['id' => 'test_tenant'])->create();
        Tenant::factory()->create();
        Tenant::all()->runForEach(function (){
            $client = new ClientRepository();

            $client->createPasswordGrantClient(null, 'Default password grant client', config('app.url'));
            $client->createPersonalAccessClient(null, 'Default personal access client', config('app.url'));
        });
    }
}
