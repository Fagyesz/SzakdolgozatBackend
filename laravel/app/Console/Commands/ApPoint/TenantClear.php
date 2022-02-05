<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Console\Commands\ApPoint;

use App\Models\Tenant;
use Config;
use Illuminate\Console\Command;
use PDO;
use PDOException;
use Stancl\Tenancy\Database\Models\Domain;

class TenantClear extends Command
{

    protected $signature = 'tenants:clear';
    protected $description = 'Clear all tenant and their databases';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->deleteModels();
            $this->deleteDatabases();
            $this->info('Dropped all tenant databases');
        }
        catch (PDOException $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }

    private function deleteDatabases(): void
    {
        $host = Config::get('database.connections.mysql.host');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');

        $pdo = new PDO("mysql:host=$host;", $username, $password);
        $arr = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($arr as $item) {
            $item = $item['Database'];
            if (str_starts_with($item,
                                Config::get('tenancy.database.prefix')
            )) $pdo->exec("DROP DATABASE IF EXISTS `$item`");
        }
    }

    private function deleteModels(): void
    {
        Domain::all()->each(fn(Domain $d) => $d->forceDelete());
        Tenant::all()->each(fn(Tenant $t) => $t->forceDelete());
    }
}
