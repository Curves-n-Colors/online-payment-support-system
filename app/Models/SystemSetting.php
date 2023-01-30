<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    public static function _storing($req)
    {
        $model = new self();
        $model->email_day      = $req->email_day;
        $model->extend_day     = $req->extend_day;

        if ($model->save()) {
            return true;
        }
        return false;
    }
}
