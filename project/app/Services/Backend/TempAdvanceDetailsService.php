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
        $model->payment_type = 'HBL';
        
        if ($model->save()) {
            return true;
        }
        return false;
    }

    public static function _store_transaction($uuid, $month, $title, $method)
    {
        $entry = self::_find_entry($uuid);
        $end_date = date('Y-m-d', strtotime($entry->start_date . ' + ' . $month . ' month'));

        $model = new TempAdvanceDetails();
        $model->pid         = NULL;
        $model->order_no    = NULL;
        $model->payment_uuid = $entry->uuid;
        $model->months      = $month;
        $model->title       = $title;
        $model->start_date  = $entry->start_date;
        $model->end_date    = $end_date;
        $model->amount      = $entry->total * $month;
        $model->payment_type = $method;

        if ($model->save()) {
            return true;
        }
        return false;
    }

    public static function _find_advance($uuid, $type){
        return TempAdvanceDetails::where('payment_uuid', $uuid)->where('payment_type', $type)->first();
    }
}
