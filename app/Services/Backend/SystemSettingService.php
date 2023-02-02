<?php

namespace App\Services\Backend;

use App\Models\SystemSetting;

class SystemSettingService
{

    public static function _storing($req)
    {
        $model = new SystemSetting();
        $model->email_day            = $req->email_day;
        $model->extend_day           = $req->extend_day;
        $model->email_send_time      = $req->email_send_time;
        if ($model->save()) {
            return true;
        }
        return false;
    }
}
