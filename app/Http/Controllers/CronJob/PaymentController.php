<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;

use App\Models\PaymentSetup;
use App\Models\CronJob;

class PaymentController extends Controller
{
    /*
    * value: YEARLY
    * encrypt: eyJpdiI6IjQxMkZ4czVxUDNXVVVqSVZ0Y0FPc3c9PSIsInZhbHVlIjoiYzVJbU0yeE4vamhDcGRCRzBBUFFVdz09IiwibWFjIjoiZDYwYzkxNmI0YTBmZmM1OGE3ZjJjYTRmNmZiMDIzM2QzNWI4NmFkMWE1NzU0Y2Y1YTkxZWZkOTg2MTI1MGZhOSJ9
    */
    public function yearly($encrypt) 
    {
        $take = 10;
        $rec_type = 'YEARLY';

        if (!CronJob::_validating($encrypt, $rec_type)) abort(401);

        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_payment_log($rec_type);
        $skip = $cron->start ?? 0;

        try 
        {
            if (now()->month == '02' && now()->daysInMonth == now()->day) {
                $setups = PaymentSetup::select('uuid')
                            ->where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', now())
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
                $setups = PaymentSetup::select('uuid')
                            ->where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', now())
                            ->whereMonth('reference_date', now()->month)
                            ->whereDay('reference_date', now()->day)
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }

            if ($setups->count() > 0) {
                self::_sending($setups);
                CronJob::_save_payment_log($cron, $rec_type, $take, $setups->count());
            }
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_payment_log($rec_type, $skip, $error->getMessage());
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
        $rec_type = 'MONTHLY';

        if (!CronJob::_validating($encrypt, $rec_type)) abort(401);

        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_payment_log($rec_type);
        $skip = $cron->start ?? 0;

        try 
        {
            if (now()->day == now()->daysInMonth) {
                $setups = PaymentSetup::select('uuid')
                            ->where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', now())

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
                                    if (now()->day == 31) {
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
                                    if (now()->day == 29) {
                                        $query2
                                        ->whereDay('reference_date', '29')
                                        ->whereMonth('reference_date', '!=', '02');
                                    }
                                    else if (now()->day == 28) {
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
            else if (now()->day == 30) {
                $setups = PaymentSetup::select('uuid')
                            ->where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', now())

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
            else if (now()->day == 28 || now()->day == 29) {
                $setups = PaymentSetup::select('uuid')
                            ->where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', now())

                            ->where(function ($query) {
                                $query
                                ->whereDay('reference_date', now()->day)
                                ->whereMonth('reference_date', '!=', '02');
                            })
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }
            else {
                $setups = PaymentSetup::select('uuid')
                            ->where('is_active', 10)
                            ->where('recurring_type', $type)
                            ->whereDate('reference_date', '<=', now())
                            ->whereDay('reference_date', now()->day)
                            ->skip($skip)
                            ->take($take)
                            ->get();
            }

            if ($setups->count() > 0) {
                self::_sending($setups);
                CronJob::_save_payment_log($cron, $rec_type, $take, $setups->count());
            }
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_payment_log($rec_type, $skip, $error->getMessage());
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
        $rec_type = 'ONETIME';

        if (!CronJob::_validating($encrypt, $rec_type)) abort(401);

        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_payment_log($rec_type);
        $skip = $cron->start ?? 0;

        try 
        {
            $setups = PaymentSetup::where('is_active', 10) 
                    ->where('recurring_type', $type)
                    ->whereDate('reference_date', now())
                    ->skip($skip)
                    ->take($take)
                    ->get();

            if ($setups->count() > 0) {
                self::_sending($setups);
                CronJob::_save_payment_log($cron, $rec_type, $take, $setups->count());
                
                foreach ($setups as $setup) {
                    $setup->is_active = 0;
                    $setup->update();
                }
            }
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_payment_log($rec_type, $skip, $error->getMessage());
        }
        echo 'process end'; die;
    }

    /*
    * value: LOGFLUSH
    * encrypt: eyJpdiI6IjBsZytyVnR3MHYrdXZBREpsQlBxK0E9PSIsInZhbHVlIjoiQkh3Ni95MVJjNG14WFo2K2VzZVRYQT09IiwibWFjIjoiMWI2MGMxNTM0ZWJiOWIzZTNlOTA0NzhjMTk5NjMwZjczY2YyNDQxYjJiMWUzYzQ5ZTY3YzIxNWRkNmNjNmIxMiJ9
    */
    public function flushing($encrypt)
    {
        if (!CronJob::_validating($encrypt, 'LOGFLUSH')) abort(401);
        CronJob::_flush_log();
    }

    public function encrypting($text)
    {
        dd('value: ' . encrypt($text));
    }

    public static function _sending($setups)
    {
        foreach ($setups as $setup) {
            PaymentSetup::_sending(['new'], $setup->uuid);
        }
    }

    /*
    public function test() 
    {
        $result = []; // check one month at a time
        $startTime = strtotime( '2019-09-25 12:00' );
        $endTime = strtotime( '2021-04-04 12:00' );

        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
            
            $today = date( 'Y-m-d', $i );
            $d = date('d', strtotime($today));
            $t = date('t', strtotime($today));

            if ($d > 27) {
                if ($d == $t) {
                    $setups = PaymentSetup::select('reference_date')
                                ->where('is_active', 10)
                                ->where('recurring_type', 3)
                                ->whereDate('reference_date', '<=', now())

                                ->where(function ($query) use ($d) {
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
                                    ->orWhere(function ($query2) use ($d) { 
                                        $query2->whereDay('reference_date', '30');
                                        if ($d == 31) {
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
                                    ->orWhere(function ($query2) use ($d) { 
                                        if ($d == 29) {
                                            $query2
                                            ->whereDay('reference_date', '29')
                                            ->whereMonth('reference_date', '!=', '02');
                                        }
                                        else if ($d == 28) {
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
                                ->get();

                    $result[$today] = [
                        'count' => $setups->count(),
                        'action' => 'last-day', 
                        'ref_date' => Arr::flatten($setups->toArray())
                    ];
                }
                else if ($d == 30) {
                    $setups = PaymentSetup::select('reference_date')
                                ->where('is_active', 10)
                                ->where('recurring_type', 3)
                                ->whereDate('reference_date', '<=', now())

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
                                ->get();

                    $result[$today] = [
                        'count' => $setups->count(),
                        'action' => '30', 
                        'ref_date' => Arr::flatten($setups->toArray())
                    ];
                }
                else if ($d == 28 || $d == 29) {
                    $setups = PaymentSetup::select('reference_date')
                                ->where('is_active', 10)
                                ->where('recurring_type', 3)
                                ->whereDate('reference_date', '<=', now())

                                ->where(function ($query) use ($d) {
                                    $query
                                    ->whereDay('reference_date', $d)
                                    ->whereMonth('reference_date', '!=', '02');
                                })
                                ->get();

                    $result[$today] = [
                        'count' => $setups->count(),
                        'action' => '28 or 29', 
                        'ref_date' => Arr::flatten($setups->toArray())
                    ];
                }
                else {
                    $setups = PaymentSetup::select('reference_date')
                                ->where('is_active', 10)
                                ->where('recurring_type', 3)
                                ->whereDate('reference_date', '<=', now())
                                ->whereDay('reference_date', $d)
                                ->get();
                    
                    $result[$today] = [
                        'count' => $setups->count(), 
                        'action' => '*27',
                        'ref_date' => Arr::flatten($setups->toArray())
                    ];
                }
            }
        } 

        dd($result);
    }
    */
}

// 31 => 1,3,5,7,8,10,12
// 30 => 4,6,9,11
// 28 => 2

/*

'2020-01-28', '2020-01-29', '2020-01-30', '2020-01-31',
'2020-02-28', '2020-02-29',
'2020-03-28', '2020-03-29', '2020-03-30', '2020-03-31',
'2020-04-28', '2020-04-29', '2020-04-30', 
'2020-05-28', '2020-05-29', '2020-05-30', '2020-05-31',
'2020-06-28', '2020-06-29', '2020-06-30', 
'2020-07-28', '2020-07-29', '2020-07-30', '2020-07-31',
'2020-08-28', '2020-08-29', '2020-08-30', '2020-08-31',
'2020-09-28', '2020-09-29', '2020-09-30', 
'2020-10-28', '2020-10-29', '2020-10-30', '2020-10-31',
'2020-11-28', '2020-11-29', '2020-11-30', 
'2020-12-28', '2020-12-29', '2020-12-30', '2020-12-31'

*/