<?php

namespace App\Jobs;

use App\Nova\Model\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Nova\Actions\CreateTenantAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 90000;

    protected $tenant, $domain;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant,$domain)
    {
        $this->tenant=$tenant;
        $this->domain=$domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // (new CreateTenantAction)($this->tenant, $this->domain);
        $tenant = Tenant::create($this->tenant + [
            'ready' => false,
            'trial_ends_at' => now()->addDays(config('tenancy.trial_days')),
        ]);
        if(env('PHP_CONFLICT') && env('PHP8_BINARY')){
            $cmd= 'cd ' . base_path() . ' && yes| '.env('PHP8_BINARY').' artisan tenant:seed --tenants=' .$tenant->id;
        }
        else{
            $cmd= 'cd ' . base_path() . ' && yes| php artisan tenant:seed --tenants=' .$tenant->id;
        }
        // echo $cmd;
        $output=shell_exec($cmd);
        echo $output;
        $tenant->ready=true;
        $tenant->save();

    }

    public function failed(\Exception $exception)
    {
        // Send user notification of failure, etc...
        echo $exception->getMessage();
    }
}
