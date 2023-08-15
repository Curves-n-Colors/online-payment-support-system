<?php

namespace App\Http\Controllers\CronJob;

use App\Models\CronJob;

use Illuminate\Http\Request;
use App\Models\HblPaymentResponse;
use App\Http\Controllers\Controller;
use App\Models\HblPaymentResultResponse;

class CheckHblPaymentController extends Controller
{
    public function update(){
        $take = 10;
        $adv_type = 'WEEK';
        $rec_type = 'ONETIME';


        $type = config('app.addons.type_recurring.'.$rec_type);
        $cron = CronJob::_get_notify_log($rec_type, $adv_type);
        $skip = $cron->start ?? 0;

        try 
        {
            $setups = HblPaymentResponse::where('payment_status', 0) 
                    ->get();
            if ($setups->count() > 0) {
                self::_update_response($setups);
                CronJob::_save_notify_log($cron, $rec_type, $adv_type, $take, $setups->count());
            }
        
        } 
        catch (\Exception $error) 
        {
            CronJob::_error_notify_log($rec_type, $adv_type, $skip, $error->getMessage());
        }
        echo 'process end'; die;
    }

    public static function _update_response($setups)
    {
        foreach ($setups as $setup) {
            HblPaymentResultResponse::_request_update($setup);
        }
    }
}
