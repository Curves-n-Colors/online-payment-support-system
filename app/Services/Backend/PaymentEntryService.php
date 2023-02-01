<?php

namespace App\Services\Backend;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendPaymentLink;
use Illuminate\Support\Str;
use App\Models\PaymentEntry;

class PaymentEntryService
{
    public static function _find($uuid)
    {
        return PaymentEntry::where('uuid', $uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {
        return PaymentEntry::orderBy('created_at', 'DESC');
    }

    public static function _change_status($uuid)
    {
        $model = self::_find($uuid);
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            LogsService::_set('Payment Entry - ' . $model->title . ' has been ' . ($model->is_active == 10 ? 'activated' : 'deactivated') . ' for Setup - ' . $model->setup->title, 'payment-entry');
            return true;
        }
        return false;
    }

    public static function _storing($data, $title, $date)
    {
        $model = new PaymentEntry();
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
            LogsService::_set('Payment Entry - ' . $model->title . ' has been created for Setup - ' . $data->title, 'payment-entry');
            return $model;
        }
        return false;
    }

    public static function _sending($uuid)
    {
        if ($model = self::_find($uuid)) {
            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid),
                'entry'     => $model->title,
                'uuid'      => $model->uuid
            ];

            Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
            LogsService::_set('Payment Entry - ' . $model->title . ' has been sent for setup - ' . $model->setup->title, 'payment-entry');
            return true;
        }
        return false;
    }


    public static function _copying($uuid)
    {
        if ($model = self::_find($uuid)) {
            LogsService::_set('Payment Entry - ' . $model->title . ' has been copied for setup - ' . $model->setup->title, 'payment-entry');
            return route('pay.index', [PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid)]);
        }
        return false;
    }

    public static function _deleting($uuid)
    {
        if ($model = self::_find($uuid)) {
            $model_title = $model->title;
            $setup_title = $model->setup->title;
            if ($model->delete()) {
                LogsService::_set('Payment Entry - ' . $model_title . ' has been deleted for Setup - ' . $setup_title, 'payment-entry');
                return true;
            }
        }
        return false;
    }

}
