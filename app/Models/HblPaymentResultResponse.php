<?php

namespace App\Models;

use App\Helpers\HBLPayment\Inquiry;
use Illuminate\Database\Eloquent\Model;

class HblPaymentResultResponse extends Model
{
    protected $table = 'hbl_payment_result_responses';

    public static function _save_response($response)
    {
        $response_array = json_decode($response, true);
        
        $code = $response_array['response']['Data'][0]['PaymentStatusInfo']['PaymentStatus'];

        $model = new self();
        $model->pid = $response_array['response']['Data'][0]['ControllerInternalId'];
        $model->order_no = $response_array['response']['Data'][0]['OrderNo'];
        $model->body = $response;
        $model->payment_status =config('app.addons.payment_status_code')[$code] ;
        $model->save();

        $hbl_inital_response = HblPaymentResponse::where('pid', $model->pid)
                                                 ->where('order_no', $model->order_no)
                                                 ->first();
        
        $hbl_inital_response->payment_status = config('app.addons.payment_status_code')[$code];
        $hbl_inital_response->update();

        if($code=='A'){
             $detail = [
                'type'   => 'HBL',
                'status' => $hbl_inital_response->payment_status
            ];
            $entry = PaymentEntry::where('uuid', $hbl_inital_response->payment_uuid)->where('is_active', 10)->first();
            PaymentDetail::_storing($entry, $detail);
            PaymentEntry::_deleting($entry->uuid);
        }
    }

    public static function _request_update($model)
    {
        $inquiry = new Inquiry();
        $response = $inquiry->ExecuteJose(config('app.addons.payment_options.hbl.merchant_id'), $model->order_no);
        self::_save_response($response);
    }
}
