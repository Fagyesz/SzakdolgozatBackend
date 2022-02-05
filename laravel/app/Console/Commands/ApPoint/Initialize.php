<?php

namespace App\Console\Commands\ApPoint;

use App\Models\Tenant;
use Artisan;
use Config;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Str;

class Initialize extends Command
{
    protected $signature = 'appoint:init {--seed=0}';
    protected $description = 'Initialize the appoint backend service';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->line('Cleaning up...');
        Artisan::call('tenants:clear');
        $this->info(Artisan::output());
        $this->line('Populating database...');
        Artisan::call('migrate:fresh --seed');
        $this->info(Artisan::output());
        $this->line('Creating passports...');
        Artisan::call('make:passport --all');
        $this->info(Artisan::output());
        $this->newLine();
        $this->info('Tenants created:');
        $this->table(['Tenant ID', 'Database', 'Url'],
                     Tenant::all()
                            ->map(fn(Tenant $t)
                                => [
                                    $t->id,
                                    Config::get('tenancy.database.prefix') . $t->id . Config::get('tenancy.database.suffix'),
                                    Config::get('app.url') . "/$t->id"
                                ])
        );
        return 0;
    }
}
