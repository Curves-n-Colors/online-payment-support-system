<?php

namespace App\Services\Backend;

use App\Models\Client;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Services\Backend\LogsService;

class CategoryService
{
    public static function _find($id)
    {
        return Category::find($id);
    }

    public static function _get($group_id = null)
    {

        return Category::orderBy('created_at', 'ASC')->get();
    }

    public static function _storing($req)
    {
        $model = new Category();
        $model->name      = $req->name;
        $model->is_active = $req->has('is_active') ? 10 : 0;
        $model->user_id   = auth()->user()->id;

        if ($model->save()) {
            LogsService::_set('Category - "' . $model->name . '" has been created', 'caetgory');
            return true;
        }
        return false;
    }

    public static function _updating($req, $id)
    {
        $model = self::_find($id);
        if (!$model) return false;

        $model->name      = $req->name;
        $model->is_active = $req->has('is_active') ? 10 : 0;

        if ($model->update()) {
            LogsService::_set('Category - "' . $model->name . '" has been updated', 'caetgory');
            return true;
        }
        return false;
    }

    public static function _change_status($id)
    {
        $model = self::_find($id);

        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            LogsService::_set('Category - "' . $model->name . '" has been ' . ($model->is_active == 10 ? 'activated' : 'deactivated'), 'caetgory');
            return true;
        }
        return false;
    }
}
