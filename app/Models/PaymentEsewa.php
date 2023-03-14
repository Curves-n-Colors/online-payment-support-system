<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentEsewa extends Model
{
    protected $table = 'payment_esewa';

    protected $fillable = [
        'order_id', 'account', 'status'
    ];

    public static function _process($params)
    {
        $response1 = self::_initiate($params); 
        $params2 = $response1['response'];
        if ($response1['status'] != 200) {
            return false; 
            // pass error to display and maintain error log
        }
        
        if ($params['pid'] != $params2['oid']) {
            return false; 
            // pass error to display and maintain error log
        }
        if ($params['amt'] != $params2['amt']) {
            return false; 
            // pass error to display and maintain error log
        }

        $response2 = self::_verify($params2); 
        if ($response2['status'] != 200) {
            return false; 
            // pass error to display and maintain error log
        }

        $params3 = [
            'order_id' => $params['order_id'],
            'refId'    => $response2['response']['refId']
        ];
        self::_create($params3);
        return true;
    }

    public static function _initiate($params)
    {
    	$esewa = config('app.addons.payment_options.esewa');

        $args = [
            'amt'   => $params['amt'], 		// sub total amount 
            'pdc'   => $params['pdc'], 	    // delivery charge
            'psc'   => $params['psc'], 	    // service charge
            'txAmt' => $params['txAmt'], 	// vat amount
            'tAmt'  => $params['tAmt'], 	// total amount
            'pid'   => $params['pid'], 	    // unique id
            'scd'   => $esewa['merchant_id'],
            'su'    => $params['su'],
            'fu'    => $params['fu'],
        ];

      
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $esewa['request_url']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = [ "response" => json_decode(curl_exec($ch), true), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE) ];
		curl_close($ch);

		return $response;

    }

    public static function _verify($params)
    {
    	$esewa = config('app.api.esewa');
        $data = [
            'amt' => $params['amt'],
            'rid' => $params['refId'],
            'pid' => $params['oid'],
            'scd' => $esewa['merchant_id'],
        ];

        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $esewa['verification_url']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = [ "response" => json_decode(curl_exec($ch), true), "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE) ];
		curl_close($ch);

		return $response;
    }

    public static function _create($params)
    {
        $id= Order::latest()->first()->id;
        // dd($id);
        $status = config('app.api.status_payment');

        $esewa = new self();
        $esewa->order_id       = $id;
        $esewa->reference_code = $params['refId'];
        $esewa->status         = $status['COMPLETED'];
        $esewa->save();
        Order::_update(['status' => 'CONFIRMED'], $id);
        return $esewa;
    }
}