<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Logs;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function _check_master($password)
    {
        $user = self::find(auth()->user()->id); 
        return Hash::check($password, $user->master_password);
    }

    public static function _storing($req)
    {
        $model = new self();
        $model->name      = $req->name;
        $model->email     = $req->email;
        $model->password  = Hash::make($req->password);
        $model->master_password = Hash::make($req->master_password);
        $model->uuid      = Str::uuid()->toString();
        $model->is_active = $req->has('is_active') ? 10 : 0;
        
        if ($model->save()) {
            Logs::_set('User - "'.$model->name.'" has been created', 'user');
            return true;
        }
        return false;
    }

    public static function _updating($req, $uuid)
    {
        $model = self::where('uuid', $uuid)->first();
        if (!$model) return false;

        $model->name    = $req->name;
        $model->email   = $req->email;
        
        if ($model->is_super != 10 && auth()->user()->id != $model->id) {
            $model->is_active = $req->has('is_active') ? 10 : 0;
        }
        
        if ($req->filled('password')) {
            $model->password = Hash::make($req->password);
        }

        if ($req->filled('master_password')) {
            $model->master_password = Hash::make($req->master_password);
        }

        if ($model->update()) {
            Logs::_set('User - "'.$model->name.'" has been updated', 'user');
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
            Logs::_set('User - "'.$model->name.'" has been ' .($model->is_active == 10 ? 'activated' : 'deactivated'), 'user');
            return true;
        }
        return false;
    }

    public static function _profiling($req)
    {
        $model = Auth::user();
        $model->name  = $req->name;
        $model->email = $req->email;
        
        Logs::_set('User - "'.$model->name.'" has been profile updated', 'user');
        return $model->update();
    }
}