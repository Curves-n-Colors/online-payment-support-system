<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

use App\Notifications\SendPaymentErrorLog;
use App\Models\Logs;


class PayKhalti extends Model
{
    // KhaltiAPI
    
    // public $publicKey = 'test_public_key_d35e3b0996b045ce990cc0aa003a6780'; 
    public $SecretKey = 'test_secret_key_64ed721ea35047ad84e5e8e53a9e8526';
    public $API_URL   = "https://khalti.com/api/v2/payment/verify/";
    public $Check_URL = 'https://khalti.com/api/v2/payment/status/?';

    public function verify($params)
    {
        $headers = ['Authorization: Key ' . $this->SecretKey];
        $args = http_build_query([
            'token'  => $params['pre_token'],
            'amount' => $params['amount']
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->API_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $response = [ "response" => json_decode(curl_exec($ch), true), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE) ];
        curl_close($ch);

        return $response;
    }

    public function check($params)
    {
        $headers = ['Authorization: Key ' . $this->SecretKey];
        $args = http_build_query([
            'token'  => $params['verified_token'], 
            'amount' => $params['amount']
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->Check_URL . $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = [ "response" => json_decode(curl_exec($ch), true), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE) ];
        curl_close($ch);

        return $response;
    }
}
