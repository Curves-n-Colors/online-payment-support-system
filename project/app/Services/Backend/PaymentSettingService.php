<?php

namespace App\Services\Backend;

use Illuminate\Support\Str;
use App\Models\PaymentSetting;


class PaymentSettingService
{
    public static function _find($uuid)
    {
        return PaymentSetting::where('uuid', $uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {

        return PaymentSetting::orderBy('created_at', 'ASC')->get();
    }

    public static function _storing($req)
    {
        $model                  = new PaymentSetting();
        $model->uuid            = Str::uuid()->toString();
        $model->payment_method  = $req->payment_method;
        $model->public_key      = $req->public_key;
        $model->secret_key      = $req->secret_key;
        $model->is_active       = $req->has('is_active') ? 10 : 0;
        $model->user_id         = auth()->user()->id;

        if ($model->save()) {
            LogsService::_set('Payment Method - "'.$model->payment_method.'" has been created', 'payment_method');
            return true;
        }
        return false;
    }

    public static function _updating($req, $uuid)
    {
        $model = self::_find($uuid);
        if (!$model) return false;
        
        $model->payment_method  = $req->payment_method;
        $model->public_key      = $req->public_key;
        $model->secret_key      = $req->secret_key;
        $model->is_active       = $req->has('is_active') ? 10 : 0;
        $model->user_id         = auth()->user()->id;

        if ($model->update()) {
            LogsService::_set('Payment Method - "'.$model->name.'" has been updated', 'payment_method');
            return true;
        }
        return false;
    }

    public static function _change_status($uuid)
    {
        $model = self::_find($uuid);

        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            LogsService::_set('Payment Method - "'.$model->name.'" has been ' .($model->is_active == 10 ? 'activated' : 'deactivated'), 'payment_method');
            return true;
        }
        return false;
    }
}
