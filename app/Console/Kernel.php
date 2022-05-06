<?php

namespace App\Console;

use App\Nova\Model\Tenant;
use Illuminate\Console\Scheduling\Schedule;
use App\ScheduledTasks\Compliance\TaskDeadlineReminder;
use App\ScheduledTasks\Compliance\UnlockFrequencyTasks;
use App\ScheduledTasks\Compliance\PassDueTasksResetStatus;
use App\ScheduledTasks\ThirdPartyRisk\FrequencyProjectUnlock;
use App\ScheduledTasks\ThirdPartyRisk\SendVendorProjectEmail;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\ScheduledTasks\PolicyManagement\SendAutoReminderEmail;
use App\ScheduledTasks\PolicyManagement\SendAcknowledgementEmail;
use App\ScheduledTasks\ThirdPartyRisk\EmailVendorProject;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //run task schedule for task deadline reminder
        // if(tenant('id')){
        //     $tenant=Tenant::where('id',tenant('id'))->first();
        //     $domain=$tenant->domains()->first();
        //     \URL::forceRootUrl('http://'.$domain->domain);
        // }

        $schedule->call(new TaskDeadlineReminder())->name('campliance-task-deadline-reminder')->withoutOverlapping()->everyMinute();

        //run task schedule for campliance-pass-due-tasks-reset-status

        $schedule->call(new PassDueTasksResetStatus())->name('campliance-pass-due-tasks-reset-status')->withoutOverlapping()->everyMinute();

        //run task schedule for campliance-UnlockFrequencyTasks

        $schedule->call(new UnlockFrequencyTasks())->name('campliance-UnlockFrequencyTasks')->withoutOverlapping()->everyMinute();

        // Policy module
        $schedule->call(new SendAcknowledgementEmail())->name('policy-SendAcknowledgementEmail')->withoutOverlapping()->everyMinute();

        //run task schedule for policy-SendAutoReminderEmail
        $schedule->call(new SendAutoReminderEmail())->name('policy-SendAutoReminderEmail')->withoutOverlapping()->everyMinute();
        
        // Third party risk module
        $schedule->call(new FrequencyProjectUnlock())->name('third-party-risk-FrequencyProjectUnlock')->withoutOverlapping()->everyMinute();
        $schedule->call(new SendVendorProjectEmail())->name('third-party-risk-SendProjectEmail')->withoutOverlapping()->everyMinute();
        // $schedule->call(new EmailVendorProject())->name('third-party-risk-EmailVendorProject')->withoutOverlapping()->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
