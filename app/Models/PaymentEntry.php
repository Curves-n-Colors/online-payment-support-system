<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use App\Notifications\SendPaymentLink;
use App\Models\PaymentSetup;
use App\Models\Logs;

class PaymentEntry extends Model
{
    protected $table = 'payment_entries';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function setup()
    {
        return $this->belongsTo('App\Models\PaymentSetup', 'payment_setup_id')->withTrashed();
    }

    public static function _storing($data, $title, $date)
    {
        $model = new self();
        $model->payment_setup_id = $data->id;
        $model->title            = $title;
        $model->client_id        = $data->client_id;
        $model->email            = $data->email;
        $model->uuid             = Str::uuid()->toString();
        $model->total            = $data->total;
        $model->currency         = $data->currency;
        $model->contents         = $data->contents;
        $model->is_active        = 10;
        $model->user_id          = auth()->check() ? auth()->user()->id : 0;
        $model->payment_options  = $data->payment_options;
        $model->payment_date     = date('Y-m-d', strtotime($date));

        if ($model->save()) {
            Logs::_set('Payment Entry - ' . $model->title . ' has been created for Setup - ' . $data->title, 'payment-entry');
            return $model;
        }
        return false;
    }

    public static function _deleting($uuid)
    {
        if ($model = self::where('uuid', $uuid)->first()) {
            $model_title = $model->title;
            $setup_title = $model->setup->title;
            if ($model->delete()) {
                Logs::_set('Payment Entry - ' . $model_title . ' has been deleted for Setup - ' . $setup_title, 'payment-entry');
                return true;
            }
        }
        return false;
    }

    public static function _change_status($uuid)
    {
        $model = self::where('uuid', $uuid)->first();
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            Logs::_set('Payment Entry - ' . $model->title . ' has been ' . ($model->is_active == 10 ? 'activated' : 'deactivated') . ' for Setup - ' . $model->setup->title, 'payment-entry');
            return true;
        }
        return false;
    }

    public static function _sending($uuid)
    { 
        if ($model = self::where('uuid', $uuid)->first()) {
            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => PaymentSetup::_encrypting($model->setup->uuid, $model->uuid),
                'entry'     => $model->title,
                'uuid'      => $model->uuid
            ];

            Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
            Logs::_set('Payment Entry - ' . $model->title . ' has been sent for setup - ' . $model->setup->title, 'payment-entry');
            return true;
        }
        return false;
    }

    public static function _copying($uuid)
    {
        if ($model = self::where('uuid', $uuid)->first()) {
            Logs::_set('Payment Entry - ' . $model->title . ' has been copied for setup - ' . $model->setup->title, 'payment-entry');
            return route('pay.index', [PaymentSetup::_encrypting($model->setup->uuid, $model->uuid)]);
        }
        return false;
    }
}
