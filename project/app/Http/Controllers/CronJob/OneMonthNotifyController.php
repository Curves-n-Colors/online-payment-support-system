<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;

use App\Models\PaymentSetup;
use App\Models\CronJob;

class OneMonthNotifyController extends Controller
{
    /*
    * value: YEARLY
    * encrypt: eyJpdiI6IjQxMkZ4czVxUDNXVVVqSVZ0Y0FPc3c9PSIsInZhbHVlIjoiYzVJbU0yeE4vamhDcGRCRzBBUFFVdz09IiwibWFjIjoiZDYwYzkxNmI0YTBmZmM1OGE3ZjJjYTRmNmZiMDIzM2QzNWI4NmFkMWE1NzU0Y2Y1YTkxZWZkOTg2MTI1MGZhOSJ9
    */
    public function yearly($encrypt) 
    {
        $take = 10;
        $adv_type = 'MONTH';
        $rec_type = 'YEARLY';

        if (!CronJob::_validating($encrypt, $rec_type)) abort(401);

        $take = 10;
        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_notify_log($rec_type, $adv_type);
        $skip = $cron->start ?? 0;
        $now  = now()->addMonths(1);

        try 
        {
            if ($now->month == '02' && $now->daysInMonth == $now->day) {
                $setups = PaymentSetup::where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereMonth('reference_date', '02')
                            ->where(function ($query) {
                                $query
                                ->where(function ($query2) { 
                                    $query2->whereDay('reference_date', '28'); 
                                })
                                ->orWhere(function ($query2) { 
                                    $query2->whereDay('reference_date', '29'); 
                                });
                            })
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }
            else {
                $setups = PaymentSetup::where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereMonth('reference_date', $now->month)
                            ->whereDay('reference_date', $now->day)
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }

            if ($setups->count() > 0) {
                self::_alerting($setups);
                CronJob::_save_notify_log($cron, $rec_type, $adv_type, $take, $setups->count());
            }
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_notify_log($rec_type, $adv_type, $skip, $error->getMessage());
        } 
        echo 'process end'; die;
    }

    /*
    * value: MONTHLY
    * encrypt: eyJpdiI6IkZPUk9reEZRQzd6Nzh0SHZldDRXYkE9PSIsInZhbHVlIjoicFhuRXJ2VTl0NmlKN2V0VEhzZkZGUT09IiwibWFjIjoiOWU4NDVkZTVlMGU1OWExNWE5MDk5MDAyNTMwNDk5YTkwNjA1NTc3YTFlNjM4ZTgxZDEyZjJkODE3ZTY4M2RjNiJ9
    */
    public function monthly($encrypt) 
    {
        $take = 10;
        $adv_type = 'MONTH';
        $rec_type = 'MONTHLY';

        if (!CronJob::_validating($encrypt, $rec_type)) abort(401);

        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_notify_log($rec_type, $adv_type);
        $skip = $cron->start ?? 0;
        $now  = now()->addMonths(1);

        try 
        {
            if ($now->day == $now->daysInMonth) {
                $setups = PaymentSetup::where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', $now)

                            ->where(function ($query) {
                                $query
                                ->where(function ($query2) { 
                                    $query2
                                    ->whereDay('reference_date', '31')
                                    ->where(function ($query3) {
                                        $query3
                                        ->where(function ($query4) { $query4->whereMonth('reference_date', '01'); })
                                        ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '03'); })
                                        ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '05'); })
                                        ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '07'); })
                                        ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '08'); })
                                        ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '10'); })
                                        ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '12'); });
                                    }); 
                                })
                                ->orWhere(function ($query2) { 
                                    $query2->whereDay('reference_date', '30');
                                    if ($now->day == 31) {
                                        $query2
                                        ->where(function ($query3) {
                                            $query3
                                            ->where(function ($query4) { $query4->whereMonth('reference_date', '04'); })
                                            ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '06'); })
                                            ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '09'); })
                                            ->orWhere(function ($query4) { $query4->whereMonth('reference_date', '11'); });
                                        });
                                    }
                                    else {
                                        $query2->whereMonth('reference_date', '!=', '02');
                                    }
                                })
                                ->orWhere(function ($query2) { 
                                    if ($now->day == 29) {
                                        $query2
                                        ->whereDay('reference_date', '29')
                                        ->whereMonth('reference_date', '!=', '02');
                                    }
                                    else if ($now->day == 28) {
                                        $query2
                                        ->whereMonth('reference_date', '!=', '02')
                                        ->where(function ($query3) {
                                            $query3
                                            ->where(function ($query4) { $query4->whereDay('reference_date', '28'); })
                                            ->orWhere(function ($query4) { $query4->whereDay('reference_date', '29'); });
                                        });
                                    }
                                })
                                ->orWhere(function ($query2) { 
                                    $query2
                                    ->whereMonth('reference_date', '02')
                                    ->where(function ($query3) {
                                        $query3
                                        ->where(function ($query4) { $query4->whereDay('reference_date', '28'); })
                                        ->orWhere(function ($query4) { $query4->whereDay('reference_date', '29'); });
                                    });
                                });
                            })
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }
            else if ($now->day == 30) {
                $setups = PaymentSetup::where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', $now)

                            ->where(function ($query) {
                                $query
                                ->whereDay('reference_date', '30')
                                ->where(function ($query2) {
                                    $query2
                                    ->where(function ($query3) { $query3->whereMonth('reference_date', '01'); })
                                    ->orWhere(function ($query3) { $query3->whereMonth('reference_date', '03'); })
                                    ->orWhere(function ($query3) { $query3->whereMonth('reference_date', '05'); })
                                    ->orWhere(function ($query3) { $query3->whereMonth('reference_date', '07'); })
                                    ->orWhere(function ($query3) { $query3->whereMonth('reference_date', '08'); })
                                    ->orWhere(function ($query3) { $query3->whereMonth('reference_date', '10'); })
                                    ->orWhere(function ($query3) { $query3->whereMonth('reference_date', '12'); });
                                });
                            })
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }
            else if ($now->day == 28 || $now->day == 29) {
                $setups = PaymentSetup::where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', $now)

                            ->where(function ($query) {
                                $query
                                ->whereDay('reference_date', $now->day)
                                ->whereMonth('reference_date', '!=', '02');
                            })
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }
            else {
                $setups = PaymentSetup::where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', $now)
                            ->whereDay('reference_date', $now->day)
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }

            if ($setups->count() > 0) {
                self::_alerting($setups);
                CronJob::_save_notify_log($cron, $rec_type, $adv_type, $take, $setups->count());
            }
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_notify_log($rec_type, $adv_type, $skip, $error->getMessage());
        } 
        echo 'process end'; die;
    }

    /*
    * value: ONETIME
    * encrypt: eyJpdiI6InBzTE9XSVppY1N5THpWTnBrMGZOMkE9PSIsInZhbHVlIjoicWI0eGVBaEdrSVhvcnhxaE5jZm02Zz09IiwibWFjIjoiMmQ2N2Y2OGVkYWFkY2E2MjcwMmUxMTg0Y2I0MTg3OGRiNmM2MmQ3NDI1NDcxZjMwZmMyYjEwZGEzMWViZjA1NiJ9
    */
    public function onetime($encrypt)
    {
        $take = 10;
        $adv_type = 'MONTH';
        $rec_type = 'ONETIME';

        if (!CronJob::_validating($encrypt, $rec_type)) abort(401);

        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_notify_log($rec_type, $adv_type);
        $skip = $cron->start ?? 0;

        try 
        {
            $setups = PaymentSetup::where('is_active', 10) 
                    ->where('recurring_type', $type)
                    ->whereDate('reference_date', now()->addMonths(1))
                    ->skip($skip)
                    ->take($take)
                    ->get();

            if ($setups->count() > 0) {
                self::_alerting($setups);
                CronJob::_save_notify_log($cron, $rec_type, $adv_type, $take, $setups->count());
            }
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_notify_log($rec_type, $adv_type, $skip, $error->getMessage());
        }
        echo 'process end'; die;
    }

    public static function _alerting($setups)
    {
        foreach ($setups as $setup) {
            PaymentSetup::_alerting('MONTH', $setup);
        }
    }
} 