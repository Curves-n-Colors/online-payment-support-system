<?php

namespace App\Services\Backend;

use App\Models\PaymentConnectIps;

class PaymentConnectIpsService
{
    public static function _create($params)
    {		
    	$connectips = new PaymentConnectIps();
		$connectips->order_id  = $params['reference_id'];
        $connectips->merchant_id  = $params['merchant_id'];
        $connectips->app_id   = $params['app_id'];
        $connectips->txn_amt  = $params['txn_amt'];
        $connectips->status   = config('app.addons.status_payment.CONFIRMED');
        $connectips->save();

        return $connectips;
    }
}