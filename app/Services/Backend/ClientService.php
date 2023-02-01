<?php

namespace App\Services\Backend;

use App\Models\Logs;
use App\Models\Client;
use Illuminate\Support\Str;

class ClientService
{
    public static function _find($uuid)
    {
        return Client::where('uuid', $uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {

        return Client::orderBy('created_at', 'ASC')->get();
    }

    public static function _storing($req)
    {
        $model = new Client();
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
        $model = self::_find($uuid);
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
        $model = self::_find($uuid);
        
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);
        
        if ($model->update()) {
            Logs::_set('Client - "'.$model->name.'" has been ' .($model->is_active == 10 ? 'activated' : 'deactivated'), 'client');
            return true;
        }
        return false;
    }
}