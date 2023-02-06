<?php

namespace App\Services\Backend;

use App\Models\SystemSetting;
use App\Models\TempAdvanceDetails;

class TempAdvanceDetailsService
{

    public static function _find_entry($uuid)
    {
        return PaymentEntryService::_find($uuid);
    }
    public static function _storing($uuid, $month, $response)
    {
        $response_array = json_decode($response, true);
        $entry = self::_find_entry($uuid);

        //GENERATE NEW END DATE
        $end_date = date('Y-m-d', strtotime($entry->start_date . ' + '.$month.' month'));

        $model = new TempAdvanceDetails();
       
        $model->pid         = $response_array['response']['Data']['paymentIncompleteResult']['controllerInternalID'];
        $model->order_no    = $response_array['response']['Data']['paymentIncompleteResult']['orderNo'];
        $model->payment_uuid= $entry->uuid;
        $model->months      = $month;
        $model->title       = $response_array['response']['Data']['paymentIncompleteResult']['productDescription'];
        $model->start_date  = $entry->start_date;
        $model->end_date    = $end_date;
        $model->amount      = $entry->total*$month;
        
        if ($model->save()) {
            return true;
        }
        return false;
    }
}
