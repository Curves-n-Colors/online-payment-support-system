<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PaymentEntry extends Model
{
    protected $table = 'payment_entries';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function setup()
    {
        return $this->belongsTo('App\Models\PaymentSetup', 'payment_setup_id')->withTrashed();
    }

    public function subscription()
    {
        return $this->belongsTo('App\Models\PaymentHasClient', 'subscription_id', 'id');
    }

}
