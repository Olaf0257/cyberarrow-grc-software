<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Nova\Model\Tenant;


class TenantCustomSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant_seed:custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom command for tenant seed.';

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
    public function handle()
    {
        $tenants=Tenant::all();
        foreach($tenants as $tenant){
            $this->info('Seeding tenant:'.$tenant->id);
            $cmd= 'cd ' . base_path() . ' && php artisan tenant:seed --tenants=' .$tenant->id.' --force';
            $output=shell_exec($cmd);
            echo $output;
        }
        return 0;
    }
}
