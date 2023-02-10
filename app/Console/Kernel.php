<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $schedule->call('App\Http\Controllers\CronJob\PaymentController@yearly', ['encrypt' => 'eyJpdiI6IjQxMkZ4czVxUDNXVVVqSVZ0Y0FPc3c9PSIsInZhbHVlIjoiYzVJbU0yeE4vamhDcGRCRzBBUFFVdz09IiwibWFjIjoiZDYwYzkxNmI0YTBmZmM1OGE3ZjJjYTRmNmZiMDIzM2QzNWI4NmFkMWE1NzU0Y2Y1YTkxZWZkOTg2MTI1MGZhOSJ9']);
        $schedule->call('App\Http\Controllers\CronJob\PaymentController@monthly', ['encrypt' => 'eyJpdiI6IkZPUk9reEZRQzd6Nzh0SHZldDRXYkE9PSIsInZhbHVlIjoicFhuRXJ2VTl0NmlKN2V0VEhzZkZGUT09IiwibWFjIjoiOWU4NDVkZTVlMGU1OWExNWE5MDk5MDAyNTMwNDk5YTkwNjA1NTc3YTFlNjM4ZTgxZDEyZjJkODE3ZTY4M2RjNiJ9']);
        $schedule->call('App\Http\Controllers\CronJob\PaymentController@onetime', ['encrypt' => 'eyJpdiI6InBzTE9XSVppY1N5THpWTnBrMGZOMkE9PSIsInZhbHVlIjoicWI0eGVBaEdrSVhvcnhxaE5jZm02Zz09IiwibWFjIjoiMmQ2N2Y2OGVkYWFkY2E2MjcwMmUxMTg0Y2I0MTg3OGRiNmM2MmQ3NDI1NDcxZjMwZmMyYjEwZGEzMWViZjA1NiJ9']);

        $schedule->call('App\Http\Controllers\CronJob\OneWeekNotifyController@onetime', ['encrypt' => 'eyJpdiI6IjQxMkZ4czVxUDNXVVVqSVZ0Y0FPc3c9PSIsInZhbHVlIjoiYzVJbU0yeE4vamhDcGRCRzBBUFFVdz09IiwibWFjIjoiZDYwYzkxNmI0YTBmZmM1OGE3ZjJjYTRmNmZiMDIzM2QzNWI4NmFkMWE1NzU0Y2Y1YTkxZWZkOTg2MTI1MGZhOSJ9']);
        $schedule->call('App\Http\Controllers\CronJob\OneWeekNotifyController@yearly', ['encrypt' => 'eyJpdiI6IkZPUk9reEZRQzd6Nzh0SHZldDRXYkE9PSIsInZhbHVlIjoicFhuRXJ2VTl0NmlKN2V0VEhzZkZGUT09IiwibWFjIjoiOWU4NDVkZTVlMGU1OWExNWE5MDk5MDAyNTMwNDk5YTkwNjA1NTc3YTFlNjM4ZTgxZDEyZjJkODE3ZTY4M2RjNiJ9']);
        $schedule->call('App\Http\Controllers\CronJob\OneWeekNotifyController@monthly', ['encrypt' => 'eyJpdiI6InBzTE9XSVppY1N5THpWTnBrMGZOMkE9PSIsInZhbHVlIjoicWI0eGVBaEdrSVhvcnhxaE5jZm02Zz09IiwibWFjIjoiMmQ2N2Y2OGVkYWFkY2E2MjcwMmUxMTg0Y2I0MTg3OGRiNmM2MmQ3NDI1NDcxZjMwZmMyYjEwZGEzMWViZjA1NiJ9']);

        $schedule->call('App\Http\Controllers\CronJob\OneMonthNotifyController@onetime', ['encrypt' => 'eyJpdiI6IjQxMkZ4czVxUDNXVVVqSVZ0Y0FPc3c9PSIsInZhbHVlIjoiYzVJbU0yeE4vamhDcGRCRzBBUFFVdz09IiwibWFjIjoiZDYwYzkxNmI0YTBmZmM1OGE3ZjJjYTRmNmZiMDIzM2QzNWI4NmFkMWE1NzU0Y2Y1YTkxZWZkOTg2MTI1MGZhOSJ9']);
        $schedule->call('App\Http\Controllers\CronJob\OneMonthNotifyController@yearly', ['encrypt' => 'eyJpdiI6InBzTE9XSVppY1N5THpWTnBrMGZOMkE9PSIsInZhbHVlIjoicWI0eGVBaEdrSVhvcnhxaE5jZm02Zz09IiwibWFjIjoiMmQ2N2Y2OGVkYWFkY2E2MjcwMmUxMTg0Y2I0MTg3OGRiNmM2MmQ3NDI1NDcxZjMwZmMyYjEwZGEzMWViZjA1NiJ9']);


        $schedule->call('App\Http\Controllers\CronJob\PaymentController@flushing', ['encrypt' => 'eyJpdiI6IjBsZytyVnR3MHYrdXZBREpsQlBxK0E9PSIsInZhbHVlIjoiQkh3Ni95MVJjNG14WFo2K2VzZVRYQT09IiwibWFjIjoiMWI2MGMxNTM0ZWJiOWIzZTNlOTA0NzhjMTk5NjMwZjczY2YyNDQxYjJiMWUzYzQ5ZTY3YzIxNWRkNmNjNmIxMiJ9']);

        $schedule->call('App\Http\Controllers\CronJob\CheckHblPaymentController@update')->everyMinute();
        
        $schedule->command('email:daily')->daily();
    }

    /**
     * Register the env for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
