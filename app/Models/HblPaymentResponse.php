<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HblPaymentResponse extends Model
{
    protected $table = 'hbl_payment_responses';

    public static function _save_response($response, $uuid){
        $response_array = json_decode($response, true);
        $model = new self();
        $model->pid = $response_array['response']['Data']['paymentIncompleteResult']['controllerInternalID'];
        $model->order_no = $response_array['response']['Data']['paymentIncompleteResult']['orderNo'];
        $model->payment_uuid = $uuid;
        $model->body = $response;
        $model->payment_status = 0;
        $model->save();
    }
}
