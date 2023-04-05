<?php

namespace App\Services\Backend;

use Illuminate\Support\Str;
use App\Models\PaymentEsewa;
use App\Models\PaymentKhalti;
use App\Models\PaymentSetting;


class PaymentKhaltiService
{
    public static function _create($entry_uuid, $transaction)
    {
        $model = new PaymentKhalti();
        $model->uuid      = $entry_uuid;
        $model->account   = $transaction['account'];
        $model->pre_token = $transaction['pre_token'];
        $model->status    = config('app.addons.status_payment.CONFIRMED');

        if ($model->save()) {
            return $model;
        }
        return false;
    }

    public static function _verify($params)
    {
        $payment_options = config('app.addons.payment_options');

        $secret = $payment_options['KHALTI']['secret_key'];
        $url = $payment_options['KHALTI']['request_url'];

        $headers = ['Authorization: Key ' . $secret];
        $args = http_build_query([
            'token'  => $params['pre_token'],
            'amount' => $params['amount']
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $response = ["response" => json_decode(curl_exec($ch), true), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE)];
        curl_close($ch);

        return $response;
    }

    public static function _update($entry_uuid, $token)
    {
        $model = PaymentKhalti::where('uuid', $entry_uuid)->first();

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
