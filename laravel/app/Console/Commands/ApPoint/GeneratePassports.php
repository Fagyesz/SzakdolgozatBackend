<?php

namespace App\Console\Commands\ApPoint;

use App\Models\Tenant;
use Artisan;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Laravel\Passport\ClientRepository;
use Schema;

class GeneratePassports extends Command
{
    protected $signature = 'make:passport {tenant?} {--N|name=Default} {--all} {--reset}';
    protected $description = 'Create passport clients for a specific tenant or for all of them';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('all')) {
            Tenant::all()->runForEach(function (Tenant $tenant){
                $this->info("Creating Passport of $tenant->name");
                if ($this->option('reset')) {
                    Schema::dropIfExists('oauth_auth_codes');
                    Schema::dropIfExists('oauth_access_tokens');
                    Schema::dropIfExists('oauth_refresh_tokens');
                    Schema::dropIfExists('oauth_clients');
                    Schema::dropIfExists('oauth_personal_access_clients');
                    Artisan::call('tenants:migrate');
                    $this->info(Artisan::output());
                }
                $this->createClient();
            });
        }else {
            /** @var Tenant $tenant */
            $tenant = Tenant::find( $this->argument('tenant'));
            $this->info("Creating Passport of $tenant->name");
            tenancy()->initialize($tenant);
            $this->createClient();
        }
        return 0;
    }

    public function createClient(): void
    {
        $client = new ClientRepository();

        $client->createPasswordGrantClient(null, $this->option('name') . ' password grant client', config('app.url'));
        $client->createPersonalAccessClient(null, $this->option('name') . ' personal access client', config('app.url'));
    }
}
