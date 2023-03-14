<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFonepay extends Model
{
    use HasFactory;

    protected $table = 'payment_fonepays';

    public static function _check($request)
    {
        $prn = $request->PRN;
        $bid = $request->BID ?? '';
        $uid = $request->UID;
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

}
