<?php

namespace App\Services\Backend;

use App\Models\SystemSetting;
use Exception;

class SystemSettingService
{

    public static function _storing($req)
    {
        try{
            foreach(array_keys($req->days_between_mail) as $value){
                $model = new SystemSetting();
                $model->recurring_type              = $value;
                $model->email_day                   = $req->email_day[$value];
                $model->days_between_mail           = $req->days_between_mail[$value];
                $model->send_email_time             = $req->send_email_time[$value];
                $model->days_between_extended_mail  = $req->days_between_extended_mail[$value];
                $model->save();
            }
            return true;
        }catch(Exception $e){
            return false;
        }
       
    }
}
