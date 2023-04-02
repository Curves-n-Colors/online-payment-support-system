<?php

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

if (!function_exists('auto_email_settings')) {

    function auto_email_settings($type)
    {
        $type = strtoupper($type);
        $recurring_type  = config('app.addons.type_recurring');
        if(isset($recurring_type[$type])){
            return [
                'recurring_type' => $recurring_type[$type],
                'email_day' => Setting::select('value')->where('slug', 'no-of-days-to-send-before-the-ending-of-subscription-for-' . strtolower($type) . '-mail')->first()->value,
                'days_between_mail' => Setting::select('value')->where('slug', 'no-of-days-to-send-email-between-days-for-' . strtolower($type) . '-mail')->first()->value,
                'send_email_time' => Setting::select('value')->where('slug', 'time-to-send-e-mails-for-' . strtolower($type) . '-mail')->first()->value,
                'days_between_extended_mail' => Setting::select('value')->where('slug', 'no-of-days-to-send-email-between-days-for-extended-period-for-' . strtolower($type) . '-mail')->first()->value
            ];
        }
        return 'The type is imvalid. Please set --type=ONETIME or --type=WEEKLY or --type=MONTHLY or --type=QUARTERLY or --type=YEARLY';
    }
       
}

if (!function_exists('vat_rate')) {

    function vat_rate():int
    {
        $value = Setting::select('value')->where('slug', 'vat-rate')->first()->value??false;
        if($value){
            return $value;
        }
        return 13;

    }
}


