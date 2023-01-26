<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\Logs;

class Client extends Model
{
    protected $table = 'clients';

    public function payment_setups()
    {
        return $this->hasMany('App\Models\PaymentSetup', 'client_id');
    }

    public function payment_entries()
    {
        return $this->hasMany('App\Models\PaymentEntry', 'client_id');
    }

    public function payment_details()
    {
        return $this->hasMany('App\Models\PaymentDetail', 'client_id');
    }

    public static function _storing($req)
    {
        $model = new self();
        $model->name      = $req->name;
        $model->email     = $req->email;
        $model->uuid      = Str::uuid()->toString();
        $model->remarks   = $req->remarks;
        $model->is_active = $req->has('is_active') ? 10 : 0;
        $model->user_id   = auth()->user()->id;

        if ($model->save()) {
            Logs::_set('Client - "'.$model->name.'" has been created', 'client');
            return true;
        }
        return false;
    }

    public static function _updating($req, $uuid)
    {
        $model = self::where('uuid', $uuid)->first();
        if (!$model) return false;

        $model->name      = $req->name;
        $model->email     = $req->email;
        $model->remarks   = $req->remarks;
        $model->is_active = $req->has('is_active') ? 10 : 0;

        if ($model->update()) {
            Logs::_set('Client - "'.$model->name.'" has been updated', 'client');
            return true;
        }
        return false;
    }

    public static function _change_status($uuid)
    {
        $model = self::where('uuid', $uuid)->first();
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);
        
        if ($model->update()) {
            Logs::_set('Client - "'.$model->name.'" has been ' .($model->is_active == 10 ? 'activated' : 'deactivated'), 'client');
            return true;
        }
        return false;
    }
}