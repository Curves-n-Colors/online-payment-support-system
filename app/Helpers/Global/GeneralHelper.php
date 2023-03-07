<?php

use Illuminate\Support\Facades\DB;



if (! function_exists('auto_email_settings')) {

    function auto_email_settings($type)
    {
        $recurring = config('app.addons.type_recurring');

        if(isset($recurring[$type])){
            return DB::table('system_settings')->where('recurring_type', $recurring[$type])->first();
        }
        return false;
    }
    
}

if (! function_exists('get_email_time')) {
    function get_email_time($type)
    {
        $recurring_type = auto_email_settings($type);
        if($recurring_type){
            $time = date('H:i',strtotime($recurring_type->send_email_time));
            return $time; 
        }
        return '6:00';
    }
}

if (! function_exists('cron_command_string')) {

    function cron_command_string($type)
    {
        $cron_string = '';

        $cron_array = [
            'minute' => '*',
            'hour'   => '*',
            'day_m'  => '*',
            'month'  => '*',
            'day_w'  => '*'
        ]; // MIN, HOUR, DAY(MONTH), MONTH, DAY(WEEK);

        $recurring_type = auto_email_settings($type);

        if($recurring_type){
            $time = strtotime($recurring_type->send_email_time);
            $between_mail = $recurring_type->days_between_mail;
    
            $cron_array['hour'] = date('H',$time);
            $cron_array['minute'] = date('i',$time);
            $cron_array['day_m'] = '*/'. $between_mail;
        }
        
        foreach($cron_array as $cron){
            $cron_string .= $cron.' ';
        }
        return $cron_string;
    }
    
}

