<?php

namespace App\Services\Backend;

use Illuminate\Support\Str;
use App\Models\PaymentEsewa;
use App\Models\PaymentFonepay;
use App\Models\PaymentSetting;


class PaymentEsewaService
{
    public static function _check($request)
    {
        $esewa_config = config('app.addons.payment_options.esewa');

        $data = [
            'amt' => $request->amt,
            'rid' => $request->refId,
            'pid' => $request->oid,
            'scd' => $esewa_config['merchant_id'],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $esewa_config['verification_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $data = (string)$xml->response_code;
        if (trim($data) == 'Success') {
            return [
                'status' => true,
            ];
        } else {
            return [
                'status' => false,
            ];
        }

        return $response;
    }

    public static function _create($request)
    {

        $model         = new PaymentEsewa();
        $model->order_id    = $request->oid;
        $model->ref_id    = $request->refId;
        $model->amount   = $request->amt;
        $model->status = 10;
        $model->save();

        return $model;
    }
}
