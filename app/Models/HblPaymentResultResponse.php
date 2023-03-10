<?php

namespace App\Models;

use App\Models\TempAdvanceDetails;
use App\Helpers\HBLPayment\Inquiry;
use Illuminate\Database\Eloquent\Model;
use App\Services\Backend\PaymentEntryService;
use App\Services\Backend\PaymentDetailService;

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
            //FOR ADVANCE PAY;
            $has_advance = TempAdvanceDetails::where('payment_uuid',$hbl_inital_response->payment_uuid)->where('pid', $model->pid)
                                                ->where('order_no', $model->order_no)
                                                // ->latest()
                                                ->first();
            if($has_advance){
                $entry->title       = $has_advance->title;
                $entry->start_date  = $has_advance->start_date;
                $entry->end_date    = $has_advance->end_date;
                $entry->total       = $has_advance->amount;
                $entry->update();

                $detail['advance_month'] = $has_advance->months;
            }
            
            PaymentDetailService::_storing($entry, $detail);
            PaymentEntryService::_update_new_entry($entry->uuid);
        }
    }

    public static function _request_update($model)
    {
        $inquiry = new Inquiry();
        $response = $inquiry->ExecuteJose(config('app.addons.payment_options.hbl.merchant_id'), $model->order_no);
        self::_save_response($response);
    }
}
