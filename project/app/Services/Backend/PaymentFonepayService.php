<?php

namespace App\Services\Backend;

use Illuminate\Support\Str;
use App\Models\PaymentFonepay;
use App\Models\PaymentSetting;


class PaymentFonepayService
{
    public static function _check($request)
    {
        $prn = $request->PRN;
        $bid = $request->BID ?? '';
        $uid = $request->UID;
        $pamt = $request->P_AMT;

        $fonepay_config = config('app.addons.payment_options.fonepay');

        $fonepay = [
            'PID' => $fonepay_config['PID'],
            'PRN' =>  $prn,
            'BID' =>  $bid,
            'UID' =>  $uid,
            'AMT' =>  $pamt
        ];

        $fonepay['DV'] = hash_hmac('sha512', $fonepay['PID'] . ',' . $fonepay['AMT'] . ',' . $fonepay['PRN'] . ',' . $fonepay['BID'] . ',' . $fonepay['UID'], $fonepay_config['sharedSecretKey']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fonepay_config['verification_url'] . '?' . http_build_query($fonepay));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = ["response" => simplexml_load_string(curl_exec($ch)), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE)];
        curl_close($ch);

        return $response;

    }

    public static function _create($response, $request)
    {
        $status = (array)$response['response'];
        
        $model         = new PaymentFonepay();
        $model->prn    = $request->PRN;
        $model->uid    = $request->UID;
        $model->pamt   = $request->P_AMT;
        $model->status = $status['success'] == 'true'?10:0;
        $model->body   = json_encode($response['response']);
        $model->save();

        return $model;
    }

}
