<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentKhalti extends Model
{
    protected $table = 'payment_khalti';

    protected $fillable = [
        'uuid', 'status', 'pre_token', 'verified_token'
    ];

    public static function _create($entry_uuid, $transaction)
    {
        $model = new self();
        $model->uuid      = $entry_uuid;
        $model->account   = $transaction['account'];
        $model->pre_token = $transaction['pre_token'];
        $model->status    = config('app.addons.status_payment.CONFIRMED');

        if ($model->save()) {
            return $model;
        }
        return false;
    }

    public static function _update($entry_uuid, $token)
    {
        $model = self::where('uuid', $entry_uuid)->first();

        if ($token) {
            $model->verified_token = $token;
            $model->status = config('app.addons.status_payment.COMPLETED');
        } else {
            $model->status = config('app.addons.status_payment.CANCELLED');
        }

        if ($model->update()) {
            return $model;
        }
        return false;
    }
}
