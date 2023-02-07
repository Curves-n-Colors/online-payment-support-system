<?php

use Illuminate\Support\Facades\DB;



if (! function_exists('system_extended_day')) {

    function system_extended_day()
    {
        return DB::table('system_settings')->first()->extend_day;
    }
    
}

if (! function_exists('system_email_send_day')) {

    function system_email_send_day()
    {
         return DB::table('system_settings')->first()->email_day;
    }

}

if (! function_exists('system_email_send_time')) {

    function system_email_send_time()
    {
         return DB::table('system_settings')->first()->email_send_time;
    }

}