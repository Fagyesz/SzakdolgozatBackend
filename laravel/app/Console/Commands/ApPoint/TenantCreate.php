<?php

namespace App\Console\Commands\ApPoint;

use App\Models\Tenant;
use Artisan;
use Illuminate\Console\Command;

class TenantCreate extends Command
{
    protected $signature = 'make:tenant {id}';
    protected $description = 'Crete a new tenant';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $t = Tenant::create([
            'id' => $this->argument('id'),
            'name' => $this->ask('What is the name of this Tenant?'),
            'description' => $this->ask('What will be the description for this tenant?')
       ]);
        Artisan::call("appoint:make:passport $t->id");
        $this->info(Artisan::output());

        Artisan::call('tenants:list');
        $this->info(Artisan::output());
        return 0;
    }
}
