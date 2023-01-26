<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    protected $table = 'email_notifications';

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function payment_entry()
    {
        return $this->belongsTo('App\Models\PaymentEntry', 'uuid', 'uuid');
    }

    public function payment_detail()
    {
        return $this->belongsTo('App\Models\PaymentDetail', 'uuid', 'uuid');
    }

    public static function _storing($data)
    {
        $model = new self();
        $model->client_id = $data->client_id;
        $model->email     = $data->email;
        $model->uuid      = $data->uuid; // either payment-entry or payment-detail
        $model->data      = json_encode($data->body);
        return $model->save();
    }
}